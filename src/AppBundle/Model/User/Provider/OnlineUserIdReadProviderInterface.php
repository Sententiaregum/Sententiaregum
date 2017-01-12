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
 * Provider that is responsible for the user ids of online users.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface OnlineUserIdReadProviderInterface
{
    /**
     * Validates all user ids in order to check which ids belong to online users.
     *
     * @param int[] $ids
     *
     * @return bool[]
     */
    public function validateUserIds(array $ids): array;
}
