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

/**
 * DTO containing the parameters for an account activation.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ActivateAccountDTO
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $activationKey;
}
