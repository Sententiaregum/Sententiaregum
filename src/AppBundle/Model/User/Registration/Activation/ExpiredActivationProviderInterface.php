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

namespace AppBundle\Model\User\Registration\Activation;

use AppBundle\Model\User\User;

/**
 * Provider that checks whether the activation attempt is expired.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface ExpiredActivationProviderInterface
{
    /**
     * Checks if the activation is expired.
     *
     * @param User $user
     *
     * @return bool
     */
    public function checkApprovalByUser(User $user);

    /**
     * Attaches a new approval at the provider.
     *
     * @param string $activationKey
     */
    public function attachNewApproval($activationKey);
}
