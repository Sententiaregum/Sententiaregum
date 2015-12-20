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

namespace AppBundle\EventListener;

use AppBundle\Event\MailerEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Custom listener that sends the event payload as email.
 *
 * @author Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * @DI\Service
 */
class MailListener
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var TwigEngine
     */
    private $engine;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * Constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     * @param TwigEngine $engine
     * @param string $defaultEmailAddress
     *
     * @DI\InjectParams({
     *     "engine"              = @DI\Inject("templating.engine.twig"),
     *     "defaultEmailAddress" = @DI\Inject("%mailer_from_address%")
     * })
     */
    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator, TwigEngine $engine, $defaultEmailAddress)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->engine = $engine;
        $this->emailAddress = $defaultEmailAddress;
    }

    /**
     * Hook that sends notifications.
     *
     * @param MailerEvent $event
     *
     * @DI\Observe(event="app.events.notification")
     */
    public function onMailEvent(MailerEvent $event)
    {
        $targets = [];
        foreach ($event->getUsers() as $user) {
            $targets[$user->getEmail()] = $user->getUsername();
        }

        $message = \Swift_Message::newInstance($this->translator->trans('NOTIFICATIONS_SUBJECT', [], 'notifications'));
        $message->setTo($targets);
        $message->setFrom([$this->emailAddress => 'Sententiaregum']);

        $message->addPart($this->renderMailPart($event, 'txt.twig'), 'text/plain');
        $message->addPart($this->renderMailPart($event, 'html.twig'), 'text/html');

        $this->mailer->send($message);
    }

    /**
     * Renders a mailing part.
     *
     * @param MailerEvent $event
     * @param string $extension
     *
     * @return string
     */
    private function renderMailPart(MailerEvent $event, $extension)
    {
        return $this->engine->render(sprintf('%s.%s', $event->getTemplateSource(), (string)$extension), $event->getParameters());
    }
}