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

namespace AppBundle\Service\Notification\Channel;

use AppBundle\Model\User\User;
use AppBundle\Service\Notification\NotificationInput;
use Swift_Mailer;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * MailingChannel.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * @author Ben Bieler <benjaminbieler2014@gmail.com>
 */
class MailingChannel implements NotificationChannelInterface
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var string
     */
    private $from;

    /**
     * Constructor.
     *
     * @param Swift_Mailer        $mailer
     * @param TranslatorInterface $translator
     * @param EngineInterface     $engine
     * @param string              $from
     */
    public function __construct(Swift_Mailer $mailer, TranslatorInterface $translator, EngineInterface $engine, string $from)
    {
        $this->mailer     = $mailer;
        $this->translator = $translator;
        $this->engine     = $engine;
        $this->from       = $from;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(NotificationInput $input)
    {
        $locale  = $this->getLocale($input);
        $message = \Swift_Message::newInstance($this->translator->trans(
            'NOTIFICATIONS_SUBJECT',
            [],
            'notifications',
            $locale
        ));

        $message->setTo($this->getTargets($input->getUsers()));
        $message->setFrom([$this->from => 'Sententiaregum']);
        $message->addPart($this->renderMailPart($input, 'txt.twig', $locale), 'text/plain');
        $message->addPart($this->renderMailPart($input, 'html.twig', $locale), 'text/html');

        $this->mailer->send($message);
    }

    /**
     * Renders the email content with a given extension (e.g. `html` or `txt`).
     *
     * @param NotificationInput $event
     * @param string            $extension
     * @param string            $locale
     *
     * @return string
     */
    private function renderMailPart(NotificationInput $event, string $extension, string $locale): string
    {
        return $this->engine->render(
            sprintf('%s.%s', $event->getTemplateSource(), $extension),
            array_merge(['locale' => $locale], $event->getParameters())
        );
    }

    /**
     * Builds a format which consists of the user's email as key and the username as value.
     *
     * @param User[] $users
     *
     * @return User[]
     */
    private function getTargets(array $users): array
    {
        return array_combine(
            array_map(function (User $user) {
                return $user->getEmail();
            }, $users),
            array_map(function (User $user) {
                return $user->getUsername();
            }, $users)
        );
    }

    /**
     * Evaluates the locale.
     *
     * @param NotificationInput $input
     *
     * @return string
     */
    private function getLocale(NotificationInput $input): string
    {
        return null === $input->getLanguage() ? $this->translator->getLocale() : $input->getLanguage();
    }
}
