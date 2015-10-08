<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Hook that updates the latest activation of a user.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service
 */
class UpdateLatestActivationListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $userManager
     * @param TokenStorageInterface  $tokenStorage
     * @param RequestStack           $requestStack
     *
     * @DI\InjectParams({
     *     "userManager"  = @DI\Inject("doctrine.orm.default_entity_manager"),
     *     "tokenStorage" = @DI\Inject("security.token_storage"),
     *     "requestStack" = @DI\Inject("request_stack")
     * })
     */
    public function __construct(
        EntityManagerInterface $userManager,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    ) {
        $this->entityManager = $userManager;
        $this->tokenStorage  = $tokenStorage;
        $this->requestStack  = $requestStack;
    }

    /**
     * Hook that updates the last action after the authentication.
     *
     * @param OnAuthenticationEvent $event
     *
     * @DI\Observe("ma27_api_key_authentication.authentication", priority=-255)
     */
    public function updateOnLogin(OnAuthenticationEvent $event)
    {
        /** @var \AppBundle\Model\User\User $user */
        $user = $event->getUser();

        // saving is not necessary since the user will be updated after triggering
        // this event during the login
        $user->setLastAction($this->getLastActionDateTimeInstance());
    }

    /**
     * Hook to be triggered after sending the response on protected routes.
     *
     * @DI\Observe("kernel.terminate", priority=-255)
     */
    public function updateAfterRequest()
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return;
        }

        /** @var \AppBundle\Model\User\User $user */
        $user = $token->getUser();
        $user->setLastAction($this->getLastActionDateTimeInstance());

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }

    /**
     * Gets the last action time.
     *
     * @return \DateTime
     */
    private function getLastActionDateTimeInstance()
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($this->requestStack->getMasterRequest()->server->get('REQUEST_TIME'));

        return $dateTime;
    }
}
