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

namespace AppBundle\Model\Core\Util;

use AppBundle\Model\Core\Provider\NotificatorInterface;
use AppBundle\Service\Notification\NotificationInput;

/**
 * Helper trait which is responsible for the notification in command handlers.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
trait NotificatableTrait
{
    /**
     * @var NotificatorInterface
     */
    private $notificator;

    /**
     * Set notificator.
     *
     * @param NotificatorInterface $notificator
     */
    public function setNotificator(NotificatorInterface $notificator): void
    {
        $this->notificator = $notificator;
    }

    /**
     * Creates the event and runs the notificator.
     *
     * @param mixed[]                      $parameters
     * @param \AppBundle\Model\User\User[] $users
     * @param string[]                     $channels
     * @param string                       $language
     * @param string|null                  $template
     *
     * @throws \LogicException If the dispatcher is not set.
     */
    public function notify(array $parameters, array $users, array $channels = [], string $language = null, string $template = null): void
    {
        if (!$this->notificator) {
            throw new \LogicException('Notification service must be set before running `NotificatableTrait::notify`!');
        }

        $event = new NotificationInput();

        if (null !== $language) {
            $event->setLanguage($language);
        }

        array_walk($users, [$event, 'addUser']);

        // can't flip and walk here since parameter values might be objects
        // and those must not be flipped.
        foreach ($parameters as $name => $value) {
            $event->addParameter($name, $value);
        }

        $this->notificator->publishNotification(get_class($this), $event, $channels, $template);
    }
}
