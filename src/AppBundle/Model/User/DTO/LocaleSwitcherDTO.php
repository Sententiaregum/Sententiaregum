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

use AppBundle\Validator\Constraints as Assert;
use AppBundle\Validator\Middleware\ValidatableDTO;

/**
 * Data transfer object that contains data for a locale switch.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LocaleSwitcherDTO extends ValidatableDTO
{
    const EMPTY_PROPERTIES = ['user'];

    /**
     * @var string
     *
     * @Assert\Locale
     */
    public $locale;

    /**
     * @var \AppBundle\Model\User\User
     */
    public $user;
}
