<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\EventListener;

use AppBundle\Event\MailerEvent;
use AppBundle\EventListener\NotificationListener;
use AppBundle\Model\User\User;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Sonata\NotificationBundle\Model\Message;
use Sonata\NotificationBundle\Model\MessageInterface;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Translation\TranslatorInterface;

class NotificationListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testPublishNotifications()
    {
        $defaultEmail = 'info@sententiaregum.dev';

        $expected = new Message();
        $expected->setBody([
            'to'      => [
                'Ma27@sententiaregum.dev'      => 'Ma27',
                'benbieler@sententiaregum.dev' => 'benbieler',
            ],
            'subject' => 'Sententiaregum Notifications',
            'message' => [
                'text' => 'Notifications',
                'html' => '<b>Notifications</b>',
            ],
            'from'    => [
                'name'  => 'Sententiaregum',
                'email' => $defaultEmail,
            ],
        ]);

        $expected->setType('mailer');
        $expected->setState(MessageInterface::STATE_OPEN);

        $event = new MailerEvent();
        $event->addUser(User::create('Ma27', '123456', 'Ma27@sententiaregum.dev'));
        $event->addUser(User::create('benbieler', '123456', 'benbieler@sententiaregum.dev'));

        $event->setTemplateSource('AppBundle:emails:test');

        $backend = $this->getMock(BackendInterface::class);
        $backend
            ->expects($this->once())
            ->method('publish')
            ->willReturnCallback(
                function (...$args) use ($expected) {
                    $message = $args[0];

                    $this->assertSame($message->getState(), $expected->getState());
                    $this->assertSame($message->getBody(), $expected->getBody());
                    $this->assertSame($message->getType(), $expected->getType());
                }
            );

        $templatingEngine = $this->getMockWithoutInvokingTheOriginalConstructor(TwigEngine::class);
        $templatingEngine
            ->expects($this->at(0))
            ->method('render')
            ->with('AppBundle:emails:test.txt.twig', [])
            ->willReturn('Notifications');

        $templatingEngine
            ->expects($this->at(1))
            ->method('render')
            ->with('AppBundle:emails:test.html.twig', [])
            ->willReturn('<b>Notifications</b>');

        $translator = $this->getMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with('NOTIFICATIONS_SUBJECT', [], 'notifications')
            ->willReturn('Sententiaregum Notifications');

        $listener = new NotificationListener($backend, $translator, $templatingEngine, $defaultEmail);
        $listener->onMailEvent($event);
    }
}
