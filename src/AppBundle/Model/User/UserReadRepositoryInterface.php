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
use AppBundle\Model\Core\DTO\PaginatableDTO;

/**
 * User repository interface which provides read access to the user model.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface UserReadRepositoryInterface
{
    /**
     * Creates a list that contains the ids of all users following a specific user.
     *
     * @param User           $user
     * @param PaginatableDTO $dto
     *
     * @return int[]
     */
    public function getFollowingIdsByUser(User $user, PaginatableDTO $dto): array;

    /**
     * Loads a user by its username and activation key.
     *
     * @param string $username
     * @param string $activationKey
     *
     * @return User|null
     */
    public function findUserByUsernameAndActivationKey(string $username, string $activationKey);

    /**
     * Filters all usernames that aren't unique.
     *
     * @param string[] $names
     *
     * @return string[]
     */
    public function filterUniqueUsernames(array $names): array;
}
