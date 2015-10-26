<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User\Registration\DTO;

use AppBundle\Validator\Constraints\Locale;
use AppBundle\Validator\Constraints\UniqueProperty;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object that contains all mandatory parameters for the first step of the registration process.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class CreateUserDTO
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="VALIDATORS_REGISTRATION_USERNAME_BLANK")
     * @Assert\Length(
     *     min="3",
     *     max="50",
     *     minMessage="VALIDATORS_REGISTRATION_USERNAME_TOO_SHORT",
     *     maxMessage="VALIDATORS_REGISTRATION_USERNAME_TOO_LONG"
     * )
     * @Assert\Regex(
     *     message="VALIDATORS_REGISTRATION_USERNAME_PATTERN",
     *     pattern="/^[A-z0-9\_\-\.]+$/i"
     * )
     *
     * @UniqueProperty(
     *     message="VALIDATORS_REGISTRATION_USERNAME_TAKEN",
     *     field="username",
     *     entity="Account:User"
     * )
     */
    private $username;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="VALIDATORS_REGISTRATION_PASSWORD_EMPTY")
     * @Assert\Length(
     *     min="4",
     *     max="4096",
     *     minMessage="VALIDATORS_REGISTRATION_PASSWORD_MIN",
     *     maxMessage="VALIDATORS_REGISTRATION_PASSWORD_MAX"
     * )
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="VALIDATORS_REGISTRATION_EMAIL_EMPTY")
     * @Assert\Email(message="VALIDATORS_REGISTRATION_INVALID_EMAIL", checkHost=true)
     *
     * @UniqueProperty(entity="Account:User", field="email", message="VALIDATORS_REGISTRATION_EMAIL_TAKEN")
     */
    private $email;

    /**
     * @var string
     *
     * @Locale(message="VALIDATORS_REGISTRATION_LOCALE")
     */
    private $locale = 'en';

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;
    }
}
