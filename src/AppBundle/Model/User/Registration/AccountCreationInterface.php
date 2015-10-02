<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User\Registration;

use AppBundle\Model\User\Registration\DTO\CreateUserDTO;

/**
 * Interface which represents the account creation step.
 */
interface AccountCreationInterface
{
    /**
     * Creates a new user.
     *
     * @param CreateUserDTO $userParameters
     *
     * @return \AppBundle\Model\User\User
     */
    public function registration(CreateUserDTO $userParameters);
}
