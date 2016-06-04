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

namespace AppBundle\Tests\Unit\EventListener;

use AppBundle\Event\MailerEvent;
use AppBundle\EventListener\MailListener;
use AppBundle\Model\User\User;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Translation\TranslatorInterface;

class MailListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testSendMail()
    {
        $defaultEmail = 'info@sententiaregum.dev';

        $event = new MailerEvent();
        $event->addUser(User::create('Ma27', '123456', 'Ma27@sententiaregum.dev'));
        $event->addUser(User::create('benbieler', '123456', 'benbieler@sententiaregum.dev'));
        $event->setTemplateSource('AppBundle:emails:test');

        $mailer = $this->getMockWithoutInvokingTheOriginalConstructor(\Swift_Mailer::class);

        $templatingEngine = $this->getMockWithoutInvokingTheOriginalConstructor(TwigEngine::class);
        $templatingEngine
            ->expects($this->at(0))
            ->method('render')
            ->with('AppBundle:emails:test.txt.twig', ['locale' => 'en'])
            ->willReturn('Notifications');

        $templatingEngine
            ->expects($this->at(1))
            ->method('render')
            ->with('AppBundle:emails:test.html.twig', ['locale' => 'en'])
            ->willReturn('<b>Notifications</b>');

        $translator = $this->getMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with('NOTIFICATIONS_SUBJECT', [], 'notifications')
            ->willReturn('Sententiaregum Notifications');

        $translator
            ->expects($this->once())
            ->method('getLocale')
            ->willReturn('en');

        $listener = new MailListener($mailer, $translator, $templatingEngine, $defaultEmail);
        $listener->onMailEvent($event);
    }
}
