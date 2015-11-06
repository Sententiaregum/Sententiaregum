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
use JMS\DiExtraBundle\Annotation as DI;
use Ma27\ApiKeyAuthenticationBundle\Event\OnFirewallAuthenticationEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Listener that marks users authenticated to the firewall as online.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service
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
     *
     * @DI\InjectParams({
     *     "provider"      = @DI\Inject("app.redis.cluster.online_users"),
     *     "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
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
     *
     * @DI\Observe(event="ma27_api_key_authentication.authorization.firewall.login")
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
