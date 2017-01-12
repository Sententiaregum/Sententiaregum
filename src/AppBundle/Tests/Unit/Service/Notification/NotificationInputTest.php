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

namespace AppBundle\Tests\Unit\Service\Notification;

use AppBundle\Model\User\User;
use AppBundle\Service\Notification\NotificationInput;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class NotificationInputTest extends \PHPUnit_Framework_TestCase
{
    public function testAddParameter(): void
    {
        $event = new NotificationInput();
        $event->addParameter('foo', 'bar');
        $this->assertCount(1, $event->getParameters());
        $this->assertSame($event->getParameters()['foo'], 'bar');
    }

    public function testAddUser(): void
    {
        $event = new NotificationInput();
        $user  = User::create('Ma27', '123456', 'foo@bar.baz', new PhpPasswordHasher());
        $event->addUser($user);

        $this->assertCount(1, $event->getUsers());
        $this->assertSame($user, $event->getUsers()[0]);
    }

    public function testSetTemplateSource(): void
    {
        $event = new NotificationInput();
        $event->setTemplateSource('@AppBundle/Resources/views/Email/notification.html.twig');
        $this->assertSame('@AppBundle/Resources/views/Email/notification.html.twig', $event->getTemplateSource());
    }

    public function testSetLocale(): void
    {
        $event = new NotificationInput();
        $event->setLanguage('de');
        $this->assertSame('de', $event->getLanguage());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot apply parameter locale since this parameter is reserved!
     */
    public function testAddInvalidParameter(): void
    {
        $event = new NotificationInput();
        $event->addParameter('locale', 'de');
    }
}
