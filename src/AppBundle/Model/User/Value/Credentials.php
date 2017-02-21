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

namespace AppBundle\Model\User\Value;

use AppBundle\Model\User\User;

/**
 * Value object containing sensitive user data like permissions.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class Credentials
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var \AppBundle\Model\User\Role[]
     */
    private $roles = [];

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $locale;

    /**
     * Factory to create a credentials object from a user entity.
     *
     * @param User $user
     *
     * @return $this
     */
    public static function fromEntity(User $user): self
    {
        return new static(
            $user->getUsername(),
            $user->getRoles(),
            $user->getApiKey(),
            $user->getLocale()
        );
    }

    /**
     * Constructor.
     *
     * @param string                       $username
     * @param \AppBundle\Model\User\Role[] $roles
     * @param string                       $apiKey
     * @param string                       $locale
     */
    public function __construct(string $username, array $roles, string $apiKey, string $locale)
    {
        $this->username = $username;
        $this->roles    = $roles;
        $this->apiKey   = $apiKey;
        $this->locale   = $locale;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get roles.
     *
     * @return \AppBundle\Model\User\Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Get apiKey.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
