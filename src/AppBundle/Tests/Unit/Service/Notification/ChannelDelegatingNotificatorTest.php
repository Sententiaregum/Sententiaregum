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

namespace AppBundle\Tests\Unit\Service\Notification;

use AppBundle\Model\User\User;
use AppBundle\Service\Notification\Channel\NotificationChannelInterface;
use AppBundle\Service\Notification\ChannelDelegatingNotificator;
use AppBundle\Service\Notification\NotificationInput;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class ChannelDelegatingNotificatorTest extends \PHPUnit_Framework_TestCase
{
    public function testPublish()
    {
        $user  = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $class = 'AppBundle\\Model\\Core\\Handler\\SecretHandler';
        $event = new NotificationInput();
        $event
            ->setLanguage('en')
            ->addParameter('foo', 'bar')
            ->addUser($user);

        $channel = $this->getMock(NotificationChannelInterface::class);
        $channel
            ->expects(self::once())
            ->method('publish')
            ->willReturn(function (NotificationInput $input) use ($event) {
                self::assertSame($input, $event);
            });

        $unusedChannel = $this->getMock(NotificationChannelInterface::class);
        $unusedChannel
            ->expects(self::never())
            ->method('publish');

        $notificator = new ChannelDelegatingNotificator([$class => 'AppBundle:Email/Secret:data'], ['mail' => $channel, 'unused' => $unusedChannel]);
        $notificator->publishNotification($class, $event, ['mail']);

        self::assertSame('AppBundle:Email/Secret:data', $event->getTemplateSource());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot generate template name for notification with name "AppBundle\Model\Core\Handler\SecretHandler"!
     */
    public function testMissingMapInfo()
    {
        $user  = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $class = 'AppBundle\\Model\\Core\\Handler\\SecretHandler';
        $event = new NotificationInput();
        $event
            ->setLanguage('en')
            ->addParameter('foo', 'bar')
            ->addUser($user);

        $notificator = new ChannelDelegatingNotificator([], []);
        $notificator->publishNotification($class, $event, []);
    }

    public function testTemplateArgumentReplacesTemplateMapConfiguration()
    {
        $channel = $this->getMock(NotificationChannelInterface::class);
        $channel
            ->expects(self::once())
            ->method('publish');

        $user  = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $class = 'AppBundle\\Model\\Core\\Handler\\SecretHandler';
        $event = new NotificationInput();
        $event
            ->setLanguage('en')
            ->addParameter('foo', 'bar')
            ->addUser($user);

        $notificator = new ChannelDelegatingNotificator([], ['mail' => $channel]);
        $notificator->publishNotification($class, $event, ['mail'], 'AppBundle:Email/Secret:data');

        self::assertSame('AppBundle:Email/Secret:data', $event->getTemplateSource());
    }
}
