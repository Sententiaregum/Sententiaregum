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

namespace AppBundle\Model\User\Registration;

/**
 * Account which represents the approval step of the registration.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface AccountApprovalInterface
{
    /**
     * Approves the new user.
     *
     * @param string $activationKey
     * @param string $username
     */
    public function approveByActivationKey(string $activationKey, string $username);
}
