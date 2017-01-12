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
 * BlockedAccountReadInterface.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface BlockedAccountReadInterface
{
    /**
     * Checks if the account is temporary blocked.
     *
     * @param string $user
     *
     * @return bool
     */
    public function isAccountTemporaryBlocked(string $user): bool;
}
