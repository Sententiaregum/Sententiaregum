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

namespace AppBundle\Model\User\Provider;

/**
 * Provider which adds user IDs to the cluster.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface OnlineUserIdWriteProviderInterface
{
    /**
     * Adds a new user id.
     *
     * @param string $userId
     *
     * @return $this
     */
    public function addUserId(string $userId): void;
}
