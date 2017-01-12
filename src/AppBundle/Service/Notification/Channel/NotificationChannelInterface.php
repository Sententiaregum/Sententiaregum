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

namespace AppBundle\Service\Notification\Channel;

use AppBundle\Service\Notification\NotificationInput;

/**
 * NotificationChannelInterface.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface NotificationChannelInterface
{
    /**
     * Publishes a notification.
     *
     * @param NotificationInput $input
     */
    public function publish(NotificationInput $input): void;
}
