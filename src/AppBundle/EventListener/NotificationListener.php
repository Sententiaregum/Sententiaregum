<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\EventListener;

use AppBundle\Event\MailerEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Sonata\NotificationBundle\Model\Message;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Hook which is responsible for sending emails.
 *
 * @DI\Service
 */
class NotificationListener
{
    /**
     * @var BackendInterface
     */
    private $backend;

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
     * @param BackendInterface    $backend
     * @param TranslatorInterface $translator
     * @param TwigEngine          $engine
     * @param string              $defaultEmailAddress
     *
     * @DI\InjectParams({
     *     "backend" = @DI\Inject("sonata.notification.backend"),
     *     "engine" = @DI\Inject("templating.engine.twig"),
     *     "defaultEmailAddress" = @DI\Inject("%mailer_from_address%")
     * })
     */
    public function __construct(BackendInterface $backend, TranslatorInterface $translator, TwigEngine $engine, $defaultEmailAddress)
    {
        $this->backend      = $backend;
        $this->translator   = $translator;
        $this->engine       = $engine;
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

        $body = new Message();
        $body->setType('mailer');
        $body->setBody([
            'to'      => $targets,
            'subject' => $this->translator->trans('NOTIFICATIONS_SUBJECT', [], 'notifications'),
            'message' => [
                'text' => $this->renderMailPart($event, 'txt.twig'),
                'html' => $this->renderMailPart($event, 'html.twig'),
            ],
            'from'    => [
                'name'  => 'Sententiaregum',
                'email' => $this->emailAddress
            ]
        ]);

        $this->backend->publish($body);
    }

    /**
     * Renders a mailing part.
     *
     * @param MailerEvent $event
     * @param string      $extension
     *
     * @return string
     */
    private function renderMailPart(MailerEvent $event, $extension)
    {
        return $this->engine->render(sprintf('%s.%s', $event->getTemplateSource(), (string) $extension), $event->getParameters());
    }
}
