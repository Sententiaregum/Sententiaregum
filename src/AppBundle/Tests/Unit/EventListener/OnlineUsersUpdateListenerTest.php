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

namespace AppBundle\Tests\EventListener;

use AppBundle\EventListener\OnlineUsersUpdateListener;
use AppBundle\Model\User\Online\OnlineUserIdDataProviderInterface;
use AppBundle\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\OnFirewallAuthenticationEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

class OnlineUsersUpdateListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateOnlineUsers()
    {
        $provider = $this->getMock(OnlineUserIdDataProviderInterface::class);
        $provider
            ->expects($this->once())
            ->method('addUserId');

        $now     = time();
        $request = Request::create('/');
        $request->server->set('REQUEST_TIME', $now);

        $requestStack = $this->getMock(RequestStack::class, ['getMasterRequest']);
        $requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->willReturn($request);

        $user          = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev');
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $token = $this->getMockWithoutInvokingTheOriginalConstructor(PreAuthenticatedToken::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $listener = new OnlineUsersUpdateListener($provider, $requestStack, $entityManager);
        $event    = new OnFirewallAuthenticationEvent();
        $event->setToken($token);
        $listener->onFirewallLogin($event);

        $this->assertSame($now, $user->getLastAction()->getTimestamp());
    }
}
