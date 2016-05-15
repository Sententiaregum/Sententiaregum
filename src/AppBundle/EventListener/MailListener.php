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
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Custom listener that sends the event payload as email.
 *
 * @author Ben Bieler <benjaminbieler2014@gmail.com>
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
     * @param \Swift_Mailer       $mailer
     * @param TranslatorInterface $translator
     * @param TwigEngine          $engine
     * @param string              $defaultEmailAddress
     */
    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator, TwigEngine $engine, $defaultEmailAddress)
    {
        $this->mailer       = $mailer;
        $this->translator   = $translator;
        $this->engine       = $engine;
        $this->emailAddress = $defaultEmailAddress;
    }

    /**
     * Hook that sends notifications.
     *
     * @param MailerEvent $event
     */
    public function onMailEvent(MailerEvent $event)
    {
        $targets = [];
        foreach ($event->getUsers() as $user) {
            $targets[$user->getEmail()] = $user->getUsername();
        }

        $locale  = null === $event->getLanguage() ? $this->translator->getLocale() : $event->getLanguage();
        $message = \Swift_Message::newInstance($this->translator->trans('NOTIFICATIONS_SUBJECT', [], 'notifications', $locale));

        $message->setTo($targets);
        $message->setFrom([$this->emailAddress => 'Sententiaregum']);
        $message->addPart($this->renderMailPart($event, 'txt.twig', $locale), 'text/plain');
        $message->addPart($this->renderMailPart($event, 'html.twig', $locale), 'text/html');

        $this->mailer->send($message);
    }

    /**
     * Renders a mailing part.
     *
     * @param MailerEvent $event
     * @param string      $extension
     * @param string      $locale
     *
     * @return string
     */
    private function renderMailPart(MailerEvent $event, $extension, $locale)
    {
        return $this->engine->render(
            sprintf('%s.%s', $event->getTemplateSource(), (string) $extension),
            array_merge(['locale' => $locale], $event->getParameters())
        );
    }
}
