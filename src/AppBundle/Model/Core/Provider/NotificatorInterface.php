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

namespace AppBundle\Model\Core\Provider;

use AppBundle\Service\Notification\NotificationInput;

/**
 * Interface for a notificator service.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
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
