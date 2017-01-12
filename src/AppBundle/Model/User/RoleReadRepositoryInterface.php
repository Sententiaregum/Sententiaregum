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

namespace AppBundle\Model\User;

/**
 * Interface which provides read access for role models.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface RoleReadRepositoryInterface
{
    /**
     * Guesses which role is the default role.
     *
     * NOTE: if the purger runs a bulk delete on users
     * there occur foreign key constraint issues with the
     * roles. Therefore roles are only allowed for
     * approved users and this default role needs to be guessed.
     *
     * @return Role
     */
    public function determineDefaultRole(): Role;
}
