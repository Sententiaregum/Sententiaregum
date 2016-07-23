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

namespace AppBundle\Model\User;

use DateTime;

/**
 * Repository which provides write access to the user model.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface UserWriteRepositoryInterface
{
    /**
     * Deletes all activations that were pending and are in a given time period.
     *
     * @param DateTime $dateTime
     *
     * @return int
     */
    public function deletePendingActivationsByDate(DateTime $dateTime): int;

    /**
     * Deletes all attempt models containing failed attempts which are too old.
     *
     * @param DateTime $dateTime
     *
     * @return int
     */
    public function deleteAncientAttemptData(DateTime $dateTime): int;

    /**
     * Saves a user and returns its UUID.
     *
     * @param User $user
     *
     * @return string
     */
    public function save(User $user): string;

    /**
     * Removes a user.
     *
     * @param User $user
     */
    public function remove(User $user);
}
