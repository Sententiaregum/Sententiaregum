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

namespace AppBundle\Tests\Unit\Event;

use AppBundle\Event\MailerEvent;
use AppBundle\Model\User\User;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class MailerEventTest extends \PHPUnit_Framework_TestCase
{
    public function testAddParameter()
    {
        $event = new MailerEvent();
        $event->addParameter('foo', 'bar');
        $this->assertCount(1, $event->getParameters());
        $this->assertSame($event->getParameters()['foo'], 'bar');
    }

    public function testAddUser()
    {
        $event = new MailerEvent();
        $user  = User::create('Ma27', '123456', 'foo@bar.baz', new PhpPasswordHasher());
        $event->addUser($user);

        $this->assertCount(1, $event->getUsers());
        $this->assertSame($user, $event->getUsers()[0]);
    }

    public function testSetTemplateSource()
    {
        $event = new MailerEvent();
        $event->setTemplateSource('@AppBundle/Resources/views/Email/notification.html.twig');
        $this->assertSame('@AppBundle/Resources/views/Email/notification.html.twig', $event->getTemplateSource());
    }

    public function setLocale()
    {
        $event = new MailerEvent();
        $event->setLanguage('de');
        $this->assertSame('de', $event->getLanguage());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot apply parameter locale since this parameter is reserved!
     */
    public function addInvalidParameter()
    {
        $event = new MailerEvent();
        $event->addParameter('locale', 'de');
    }
}
