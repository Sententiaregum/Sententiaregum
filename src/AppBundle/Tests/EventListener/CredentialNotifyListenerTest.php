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

use AppBundle\EventListener\CredentialNotifyListener;
use AppBundle\Model\Ip\IpLocation;
use AppBundle\Model\Ip\Tracer\IpTracingServiceInterface;
use AppBundle\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Event\OnInvalidCredentialsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CredentialNotifyListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testNotifyNewIpOnLogin()
    {
        $user = User::create('Ma27', '123456', 'foo@bar.de');

        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $eventDispatcher = $this->getMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $stack   = new RequestStack();
        $stack->push($request);

        $tracer = $this->getMock(IpTracingServiceInterface::class, ['getIpLocationData']);
        $tracer
            ->expects($this->any())
            ->method('getIpLocationData')
            ->willReturn(new IpLocation('127.0.0.1', 'Germany', 'Bavaria', 'Munich', 48, 11));

        $listener = new CredentialNotifyListener($entityManager, $eventDispatcher, $stack, $tracer);
        $listener->onAuthentication(new OnAuthenticationEvent($user));

        $this->assertFalse($user->isNewUserIp('127.0.0.1'));
    }

    public function testNotifyOnMultipleAuthAttempts()
    {
        $user = User::create('Ma27', '123456', 'foo@bar.de');
        $user->addFailedAuthenticationWithIp('127.0.0.1');
        $user->addFailedAuthenticationWithIp('127.0.0.1');

        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $eventDispatcher = $this->getMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $stack   = new RequestStack();
        $stack->push($request);

        $tracer = $this->getMock(IpTracingServiceInterface::class, ['getIpLocationData']);
        $tracer
            ->expects($this->any())
            ->method('getIpLocationData')
            ->willReturn(new IpLocation('127.0.0.1', 'Germany', 'Bavaria', 'Munich', 48, 11));

        $listener = new CredentialNotifyListener($entityManager, $eventDispatcher, $stack, $tracer);
        $listener->onFailedAuthentication(new OnInvalidCredentialsEvent($user));

        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum('127.0.0.1'));
    }

    public function testNoNotificationIfUsernameIsWrong()
    {
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->never())
            ->method('persist');

        $eventDispatcher = $this->getMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $stack   = new RequestStack();
        $stack->push($request);

        $tracer = $this->getMock(IpTracingServiceInterface::class, ['getIpLocationData']);
        $tracer
            ->expects($this->never())
            ->method('getIpLocationData');

        $listener = new CredentialNotifyListener($entityManager, $eventDispatcher, $stack, $tracer);
        $listener->onFailedAuthentication(new OnInvalidCredentialsEvent());
    }
}
