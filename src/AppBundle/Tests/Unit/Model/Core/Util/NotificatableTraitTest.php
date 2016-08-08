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

namespace AppBundle\Tests\Unit\Model\Core\Util;

use AppBundle\Model\Core\Provider\NotificatorInterface;
use AppBundle\Model\Core\Util\NotificatableTrait;
use AppBundle\Model\User\User;
use AppBundle\Service\Notification\NotificationInput;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class NotificatableTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Notification service must be set before running `NotificatableTrait::notify`!
     */
    public function testMissingNotificator()
    {
        $trait = $this->getMockForTrait(NotificatableTrait::class);
        $trait->notify([], [], [], 'en');
    }

    public function testConfigureData()
    {
        $trait     = $this->getMockForTrait(NotificatableTrait::class);
        $publisher = $this->getMock(NotificatorInterface::class);

        $class = get_class($trait);
        $publisher
            ->expects($this->once())
            ->method('publishNotification')
            ->willReturnCallback(function (string $className, NotificationInput $event, array $channels) use ($class) {
                self::assertSame($className, $class);
                self::assertCount(1, $event->getUsers());
                self::assertNull($event->getLanguage());
                self::assertCount(1, $event->getParameters());
                self::assertSame('bar', $event->getParameters()['foo']);
                self::assertSame($channels, ['mail']);

                $user = $event->getUsers()[0];
                self::assertSame('Ma27', $user->getUsername());
            });

        $user = User::create('Ma27', '123456', 'ma27@sententiaregum.dev', new PhpPasswordHasher());
        $trait->setNotificator($publisher);

        $trait->notify(['foo' => 'bar'], [$user], ['mail']);
    }
}
