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

namespace AppBundle\Model\User\Online;

/**
 * Provider that is responsible for the user ids of online users.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface OnlineUserIdDataProviderInterface
{
    /**
     * Adds a new user id.
     *
     * @param string $userId
     *
     * @return $this
     */
    public function addUserId(string $userId);

    /**
     * Validates all user ids in order to check which ids belong to online users.
     *
     * @param int[] $ids
     *
     * @return bool[]
     */
    public function validateUserIds(array $ids): array;
}
