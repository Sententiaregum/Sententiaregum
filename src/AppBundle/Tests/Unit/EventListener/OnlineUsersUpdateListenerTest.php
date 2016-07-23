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

namespace AppBundle\Tests\EventListener;

use AppBundle\EventListener\OnlineUsersUpdateListener;
use AppBundle\Model\User\Provider\OnlineUserIdWriteProviderInterface;
use AppBundle\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\OnFirewallAuthenticationEvent;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

class OnlineUsersUpdateListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateOnlineUsers()
    {
        $provider = $this->getMock(OnlineUserIdWriteProviderInterface::class);
        $provider
            ->expects($this->once())
            ->method('addUserId');

        $user          = $this->getMockWithoutInvokingTheOriginalConstructor(User::class);
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);

        $user->expects($this->once())->method('updateLastAction');
        $user->expects($this->once())->method('getId')->willReturn('1');

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $token = $this->getMockWithoutInvokingTheOriginalConstructor(PreAuthenticatedToken::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $listener = new OnlineUsersUpdateListener($provider, $entityManager);
        $event    = new OnFirewallAuthenticationEvent();
        $event->setToken($token);
        $listener->onFirewallLogin($event);
    }
}
