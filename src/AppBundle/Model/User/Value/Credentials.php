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

namespace AppBundle\Model\User\Value;

use AppBundle\Model\User\User;

/**
 * Value object containing sensitive user data like permissions.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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
    public static function fromEntity(User $user)
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
    public function __construct($username, array $roles, $apiKey, $locale)
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get roles.
     *
     * @return \AppBundle\Model\User\User[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get apiKey.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
