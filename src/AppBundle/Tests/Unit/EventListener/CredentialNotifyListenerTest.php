<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Tests\Unit\EventListener;

use AppBundle\EventListener\CredentialNotifyListener;
use AppBundle\Model\Core\Provider\NotificatorInterface;
use AppBundle\Model\Ip\Provider\IpTracingServiceInterface;
use AppBundle\Model\Ip\Value\IpLocation;
use AppBundle\Model\User\Provider\BlockedAccountWriteProviderInterface;
use AppBundle\Model\User\User;
use AppBundle\Model\User\Util\Date\DateTimeComparison;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Event\OnInvalidCredentialsEvent;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CredentialNotifyListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testNotifyNewIpOnLogin(): void
    {
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $stack   = new RequestStack();
        $stack->push($request);

        $tracer = $this->createMock(IpTracingServiceInterface::class);
        $tracer
            ->expects($this->any())
            ->method('getIpLocationData')
            ->willReturn(new IpLocation('127.0.0.1', 'Germany', 'Bavaria', 'Munich', 48, 11));

        $notificator = $this->createMock(NotificatorInterface::class);
        $notificator
            ->expects(self::once())
            ->method('publishNotification');

        $listener = new CredentialNotifyListener($entityManager, $stack, $tracer, $this->createMock(BlockedAccountWriteProviderInterface::class));
        $listener->setNotificator($notificator);
        $listener->onAuthentication(new OnAuthenticationEvent($user));

        $this->assertFalse($user->addAndValidateNewUserIp('127.0.0.1', new DateTimeComparison()));
    }

    public function testNotifyOnMultipleAuthAttempts(): void
    {
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());
        $user->addFailedAuthenticationWithIp('127.0.0.1');
        $user->addFailedAuthenticationWithIp('127.0.0.1');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $stack   = new RequestStack();
        $stack->push($request);

        $tracer = $this->createMock(IpTracingServiceInterface::class);
        $tracer
            ->expects($this->any())
            ->method('getIpLocationData')
            ->willReturn(new IpLocation('127.0.0.1', 'Germany', 'Bavaria', 'Munich', 48, 11));

        $provider = $this->createMock(BlockedAccountWriteProviderInterface::class);
        $provider
            ->expects($this->once())
            ->method('addTemporaryBlockedAccountID')
            ->with($user->getId());

        $notificator = $this->createMock(NotificatorInterface::class);
        $notificator
            ->expects(self::once())
            ->method('publishNotification');

        $listener = new CredentialNotifyListener($entityManager, $stack, $tracer, $provider);
        $listener->setNotificator($notificator);
        $listener->onFailedAuthentication(new OnInvalidCredentialsEvent($user));
    }

    public function testNoNotificationIfUsernameIsWrong(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->never())
            ->method('persist');

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $stack   = new RequestStack();
        $stack->push($request);

        $tracer = $this->createMock(IpTracingServiceInterface::class);
        $tracer
            ->expects($this->never())
            ->method('getIpLocationData');

        $notificator = $this->createMock(NotificatorInterface::class);
        $notificator
            ->expects(self::never())
            ->method('publishNotification');

        $listener = new CredentialNotifyListener($entityManager, $stack, $tracer, $this->createMock(BlockedAccountWriteProviderInterface::class));
        $listener->setNotificator($notificator);
        $listener->onFailedAuthentication(new OnInvalidCredentialsEvent());
    }
}
