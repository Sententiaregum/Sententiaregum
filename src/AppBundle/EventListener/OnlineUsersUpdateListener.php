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

namespace AppBundle\EventListener;

use AppBundle\Model\User\Online\OnlineUserIdDataProviderInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\OnFirewallAuthenticationEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Listener that marks users authenticated to the firewall as online.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OnlineUsersUpdateListener
{
    /**
     * @var OnlineUserIdDataProviderInterface
     */
    private $userIdProvider;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param OnlineUserIdDataProviderInterface $provider
     * @param RequestStack                      $requestStack
     * @param EntityManagerInterface            $entityManager
     */
    public function __construct(
        OnlineUserIdDataProviderInterface $provider,
        RequestStack $requestStack,
        EntityManagerInterface $entityManager
    ) {
        $this->userIdProvider = $provider;
        $this->requestStack   = $requestStack;
        $this->entityManager  = $entityManager;
    }

    /**
     * Marks users successfully authenticated to the firewall as online.
     *
     * @param OnFirewallAuthenticationEvent $event
     */
    public function onFirewallLogin(OnFirewallAuthenticationEvent $event)
    {
        /** @var \AppBundle\Model\User\User $user */
        $user = $event->getToken()->getUser();
        $this->userIdProvider->addUserId($user->getId());

        $user->setLastAction(
            new DateTime(sprintf('@%s', $this->requestStack->getMasterRequest()->server->get('REQUEST_TIME')))
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }
}
