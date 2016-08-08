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

use AppBundle\Service\Notification\NotificationInput;

/**
 * NotificationChannelInterface.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface NotificationChannelInterface
{
    /**
     * Publishes a notification.
     *
     * @param NotificationInput $input
     */
    public function publish(NotificationInput $input);
}
