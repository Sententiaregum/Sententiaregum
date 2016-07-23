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

namespace AppBundle\Model\Core\Util;

use AppBundle\Event\MailerEvent;
use AppBundle\Model\Core\Provider\NotificatorInterface;

/**
 * Helper trait which is responsible for the notification in command handlers.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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
    public function setNotificator(NotificatorInterface $notificator)
    {
        $this->notificator = $notificator;
    }

    /**
     * Creates the event and runs the notificator.
     *
     * @param string[]                     $parameters
     * @param \AppBundle\Model\User\User[] $users
     * @param string                       $language
     *
     * @throws \LogicException If the dispatcher is not set.
     */
    public function notify(array $parameters, array $users, string $language = 'en')
    {
        if (!$this->notificator) {
            throw new \LogicException('Dispatcher must be set before running `NotificatableTrait::notify`!');
        }

        $args  = array_flip($parameters);
        $event = new MailerEvent();
        $event->setLanguage($language);

        array_walk($users, [$event, 'addUser']);
        array_walk($args, [$event, 'addParameter']);

        $this->notificator->publishNotification(get_class($this), $event);
    }
}
