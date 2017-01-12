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

namespace AppBundle\Validator\Middleware;

/**
 * Simple baseclass for a DTO that configures the validation behavior.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
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
     * @return bool
     */
    public function shouldValidate(): bool
    {
        return $this->validate;
    }

    /**
     * @return bool
     */
    public function shouldContinueOnInvalid(): bool
    {
        return $this->continueOnInvalid;
    }

    /**
     * @return ValidationInfo
     */
    public function getInfo(): ?ValidationInfo
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
    public function setValidationInfo(ValidationInfo $validationInfo): self
    {
        if ($this->info) {
            throw new \LogicException('Cannot override the whole validation info! Please use the getter and modify the reference!');
        }

        $this->info = $validationInfo;

        return $this;
    }
}
