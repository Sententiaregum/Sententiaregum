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

use AppBundle\Event\MailerEvent;
use AppBundle\Model\User\User;
use AppBundle\Service\Notification\EventDispatchingNotificator;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatchingNotificatorTest extends \PHPUnit_Framework_TestCase
{
    public function testDispatch()
    {
        $user  = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $class = 'AppBundle\\Model\\Core\\Handler\\SecretHandler';
        $event = new MailerEvent();
        $event
            ->setLanguage('en')
            ->addParameter('foo', 'bar')
            ->addUser($user);

        $dispatcher = $this->getMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(MailerEvent::EVENT_NAME, $event);

        $notificator = new EventDispatchingNotificator($dispatcher, [$class => 'AppBundle:Email/Secret:data']);
        $notificator->publishNotification($class, $event);

        $this->assertSame('AppBundle:Email/Secret:data', $event->getTemplateSource());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot generate template name for notification with name "AppBundle\Model\Core\Handler\SecretHandler"!
     */
    public function testMissingMapInfo()
    {
        $user  = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $class = 'AppBundle\\Model\\Core\\Handler\\SecretHandler';
        $event = new MailerEvent();
        $event
            ->setLanguage('en')
            ->addParameter('foo', 'bar')
            ->addUser($user);

        $dispatcher = $this->getMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->never())
            ->method('dispatch');

        $notificator = new EventDispatchingNotificator($dispatcher, []);
        $notificator->publishNotification($class, $event);
    }
}
