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

use AppBundle\Model\Core\Util\NotificatableTrait;
use AppBundle\Model\Ip\Provider\IpTracingServiceInterface;
use AppBundle\Model\User\Provider\BlockedAccountWriteProviderInterface;
use AppBundle\Model\User\User;
use AppBundle\Model\User\Util\Date\DateTimeComparison;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\AbstractUserEvent;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Event\OnInvalidCredentialsEvent;
use Ma27\ApiKeyAuthenticationBundle\Ma27ApiKeyAuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Listener which sends notification after logins and failed authentications.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class CredentialNotifyListener implements EventSubscriberInterface
{
    use NotificatableTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var IpTracingServiceInterface
     */
    private $ipTracer;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var BlockedAccountWriteProviderInterface
     */
    private $accountBlockerProvider;

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
     * @param EntityManagerInterface               $entityManager
     * @param RequestStack                         $requestStack
     * @param IpTracingServiceInterface            $ipTracer
     * @param BlockedAccountWriteProviderInterface $accountBlockerProvider
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack,
        IpTracingServiceInterface $ipTracer,
        BlockedAccountWriteProviderInterface $accountBlockerProvider
    ) {
        $this->entityManager          = $entityManager;
        $this->requestStack           = $requestStack;
        $this->ipTracer               = $ipTracer;
        $this->accountBlockerProvider = $accountBlockerProvider;
    }

    /**
     * Hook to be triggered on failed authentication.
     *
     * @param OnInvalidCredentialsEvent $event
     */
    public function onFailedAuthentication(OnInvalidCredentialsEvent $event): void
    {
        if (null === $user = $this->getUser($event)) {
            return;
        }

        $masterRequest = $this->requestStack->getMasterRequest();
        $user->addFailedAuthenticationWithIp($masterRequest->getClientIp());
        if ($user->exceedsIpFailedAuthAttemptMaximum($masterRequest->getClientIp(), new DateTimeComparison())) {
            $this->dispatchNotificationEvent(
                $user,
                'failed_authentication'
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
    public function onAuthentication(OnAuthenticationEvent $event): void
    {
        $user = $this->getUser($event);

        if ($user->addAndValidateNewUserIp($this->requestStack->getMasterRequest()->getClientIp(), new DateTimeComparison())) {
            $this->dispatchNotificationEvent(
                $user,
                'new_login'
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
     *
     * @throws \LogicException
     */
    private function dispatchNotificationEvent(User $user, string $templateName): void
    {
        $this->accountBlockerProvider->addTemporaryBlockedAccountID($user->getId());

        $this->notify(
            [
                'notification_target' => $user,
                'tracing_data'        => $this->ipTracer->getIpLocationData(
                    $this->requestStack->getMasterRequest()->getClientIp(),
                    $user->getLocale()
                ),
            ],
            [$user],
            ['mail'],
            null,
            sprintf('AppBundle:Email/AuthAttempt:%s', $templateName)
        );
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
    private function getUser(AbstractUserEvent $event): ?User
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
