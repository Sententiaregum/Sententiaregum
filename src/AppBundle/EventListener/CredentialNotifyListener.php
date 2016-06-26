<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\EventListener;

use AppBundle\Event\MailerEvent;
use AppBundle\Model\Ip\Tracer\IpTracingServiceInterface;
use AppBundle\Model\User\User;
use AppBundle\Model\User\Util\DateTimeComparison;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\AbstractUserEvent;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Event\OnInvalidCredentialsEvent;
use Ma27\ApiKeyAuthenticationBundle\Ma27ApiKeyAuthenticationEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Listener which sends notification after logins and failed authentications.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class CredentialNotifyListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var IpTracingServiceInterface
     */
    private $ipTracer;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var DateTimeComparison
     */
    private $comparison;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Ma27ApiKeyAuthenticationEvents::AUTHENTICATION     => 'onAuthentication',
            Ma27ApiKeyAuthenticationEvents::CREDENTIAL_FAILURE => 'onFailedAuthentication',
        ];
    }

    /**
     * Constructor.
     *
     * @param EntityManagerInterface    $entityManager
     * @param EventDispatcherInterface  $eventDispatcher
     * @param RequestStack              $requestStack
     * @param IpTracingServiceInterface $ipTracer
     * @param DateTimeComparison        $comparison
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        IpTracingServiceInterface $ipTracer,
        DateTimeComparison $comparison
    ) {
        $this->entityManager   = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack    = $requestStack;
        $this->ipTracer        = $ipTracer;
        $this->comparison      = $comparison;
    }

    /**
     * Hook to be triggered on failed authentication.
     *
     * @param OnInvalidCredentialsEvent $event
     */
    public function onFailedAuthentication(OnInvalidCredentialsEvent $event)
    {
        if (null === $user = $this->getUser($event)) {
            return;
        }

        $masterRequest = $this->requestStack->getMasterRequest();
        $user->addFailedAuthenticationWithIp($masterRequest->getClientIp());
        if ($user->exceedsIpFailedAuthAttemptMaximum($masterRequest->getClientIp(), $this->comparison)) {
            $this->dispatchNotificationEvent(
                $user,
                'failed_authentication',
                MailerEvent::EVENT_NAME
            );
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Hook on successful authentication.
     *
     * @param OnAuthenticationEvent $event
     */
    public function onAuthentication(OnAuthenticationEvent $event)
    {
        $user = $this->getUser($event);

        if ($user->addAndValidateNewUserIp($this->requestStack->getMasterRequest()->getClientIp(), $this->comparison)) {
            $this->dispatchNotificationEvent(
                $user,
                'new_login',
                MailerEvent::EVENT_NAME
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    /**
     * Dispatches the mailer event.
     *
     * @param User   $user
     * @param string $templateName
     * @param string $eventName
     */
    private function dispatchNotificationEvent(User $user, string $templateName, string $eventName)
    {
        $event = new MailerEvent();
        $event->addUser($user)
            ->addParameter('notification_target', $user)
            ->addParameter(
                'tracing_data',
                $this->ipTracer->getIpLocationData(
                    $this->requestStack->getMasterRequest()->getClientIp(),
                    $user->getLocale()
                )
            )
            ->setTemplateSource(sprintf('AppBundle:Email/AuthAttempt:%s', $templateName));

        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * Extracts and checks the user from the event object.
     *
     * @param AbstractUserEvent $event
     *
     * @throws \LogicException If the user model is invalid.
     *
     * @return User
     */
    private function getUser(AbstractUserEvent $event)
    {
        $user = $event->getUser();
        if (!empty($user) && !$user instanceof User) {
            throw new \LogicException(sprintf(
                'User can be null or must be an instance of "%s", but found instance of "%s"!',
                User::class,
                get_class($user)
            ));
        }

        return $user;
    }
}
