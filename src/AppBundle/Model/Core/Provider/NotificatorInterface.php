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

namespace AppBundle\Model\Core\Provider;

use AppBundle\Service\Notification\NotificationInput;

/**
 * Interface for a notificator service.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface NotificatorInterface
{
    /**
     * Publishes a notification.
     *
     * @param string            $name
     * @param NotificationInput $event
     * @param string[]          $channels
     * @param string|null       $template
     */
    public function publishNotification(string $name, NotificationInput $event, array $channels, string $template = null): void;
}
