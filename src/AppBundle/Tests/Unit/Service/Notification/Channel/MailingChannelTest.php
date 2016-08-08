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

namespace AppBundle\Tests\Unit\Service\Notification\Channel;

use AppBundle\Model\User\User;
use AppBundle\Service\Notification\Channel\MailingChannel;
use AppBundle\Service\Notification\NotificationInput;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MailingChannelTest extends \PHPUnit_Framework_TestCase
{
    public function testSendMail()
    {
        $defaultEmail = 'info@sententiaregum.dev';

        $input = new NotificationInput();
        $input->addUser(User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher()));
        $input->addUser(User::create('benbieler', '123456', 'benbieler@sententiaregum.dev', new PhpPasswordHasher()));
        $input->setTemplateSource('AppBundle:emails:test');

        $mailer = $this->getMockWithoutInvokingTheOriginalConstructor(\Swift_Mailer::class);
        $mailer
            ->expects(self::once())
            ->method('send')
            ->willReturnCallback(function (\Swift_Message $message) {
                self::assertSame($message->getTo(), ['Ma27@sententiaregum.dev' => 'Ma27', 'benbieler@sententiaregum.dev' => 'benbieler']);
                self::assertSame($message->getFrom(), ['info@sententiaregum.dev' => 'Sententiaregum']);
            });

        $templatingEngine = $this->getMockWithoutInvokingTheOriginalConstructor(EngineInterface::class);
        $templatingEngine
            ->expects(self::at(0))
            ->method('render')
            ->with('AppBundle:emails:test.txt.twig', ['locale' => 'en'])
            ->willReturn('Notifications');

        $templatingEngine
            ->expects(self::at(1))
            ->method('render')
            ->with('AppBundle:emails:test.html.twig', ['locale' => 'en'])
            ->willReturn('<b>Notifications</b>');

        $translator = $this->getMock(TranslatorInterface::class);
        $translator
            ->expects(self::once())
            ->method('trans')
            ->with('NOTIFICATIONS_SUBJECT', [], 'notifications')
            ->willReturn('Sententiaregum Notifications');

        $translator
            ->expects(self::once())
            ->method('getLocale')
            ->willReturn('en');

        $listener = new MailingChannel($mailer, $translator, $templatingEngine, $defaultEmail);
        $listener->publish($input);
    }
}
