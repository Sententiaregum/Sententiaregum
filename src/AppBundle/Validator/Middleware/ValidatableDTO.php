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

namespace AppBundle\Validator\Middleware;

/**
 * Simple baseclass for a DTO that configures the validation behavior.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
abstract class ValidatableDTO
{
    /**
     * @var bool
     */
    protected $validate = true;

    /**
     * @var bool
     */
    protected $continueOnInvalid = false;

    /**
     * @var ValidationInfo
     */
    protected $info;

    /**
     * @return boolean
     */
    public function shouldValidate(): bool
    {
        return $this->validate;
    }

    /**
     * @return boolean
     */
    public function shouldContinueOnInvalid(): bool
    {
        return $this->continueOnInvalid;
    }

    /**
     * @return ValidationInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Adds a validation info instance.
     *
     * @param ValidationInfo $validationInfo
     *
     * @throws \LogicException If the validation info is already set.
     *
     * @return $this
     */
    public function setValidationInfo(ValidationInfo $validationInfo)
    {
        if ($this->info) {
            throw new \LogicException('Cannot override the whole validation info! Please use the getter and modify the reference!');
        }

        $this->info = $validationInfo;

        return $this;
    }
}
