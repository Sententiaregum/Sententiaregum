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

use AppBundle\Model\User\Provider\OnlineUserIdWriteProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\OnFirewallAuthenticationEvent;

/**
 * Listener that marks users authenticated to the firewall as online.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OnlineUsersUpdateListener
{
    /**
     * @var OnlineUserIdWriteProviderInterface
     */
    private $userIdProvider;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param OnlineUserIdWriteProviderInterface $provider
     * @param EntityManagerInterface             $entityManager
     */
    public function __construct(
        OnlineUserIdWriteProviderInterface $provider,
        EntityManagerInterface $entityManager
    ) {
        $this->userIdProvider = $provider;
        $this->entityManager  = $entityManager;
    }

    /**
     * Marks users successfully authenticated to the firewall as online.
     *
     * @param OnFirewallAuthenticationEvent $event
     */
    public function onFirewallLogin(OnFirewallAuthenticationEvent $event): void
    {
        /** @var \AppBundle\Model\User\User $user */
        $user = $event->getToken()->getUser();
        $this->userIdProvider->addUserId($user->getId());

        $user->updateLastAction();

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
