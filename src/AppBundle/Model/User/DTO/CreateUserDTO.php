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

namespace AppBundle\Model\User\DTO;

use AppBundle\Model\User\User;
use AppBundle\Validator\Constraints\Locale;
use AppBundle\Validator\Constraints\UniqueDTOParams;
use AppBundle\Validator\Middleware\ValidatableDTO;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object that contains all mandatory parameters for the first step of the registration process.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @UniqueDTOParams(
 *     fieldConfig={
 *         {
 *             "message"="VALIDATORS_REGISTRATION_USERNAME_TAKEN",
 *             "field"="username",
 *             "entity"="Account:User"
 *         },
 *         {
 *             "entity"="Account:User",
 *             "field"="email",
 *             "message"="VALIDATORS_REGISTRATION_EMAIL_TAKEN"
 *         }
 *     }
 * )
 */
class CreateUserDTO extends ValidatableDTO
{
    const EMPTY_PROPERTIES = ['user'];
    const SUGGESTIONS      = 'suggestions';

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
     *     pattern="/^[A-z0-9_\-\.]+$/i"
     * )
     */
    public $username;

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
    public $password;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="VALIDATORS_REGISTRATION_EMAIL_EMPTY")
     * @Assert\Email(message="VALIDATORS_REGISTRATION_INVALID_EMAIL")
     */
    public $email;

    /**
     * @var string
     *
     * @Locale(message="VALIDATORS_REGISTRATION_LOCALE")
     */
    public $locale = 'en';

    /**
     * @var User
     */
    public $user;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->continueOnInvalid = true;
    }
}
