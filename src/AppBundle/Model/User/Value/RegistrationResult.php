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
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Result object.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class RegistrationResult
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $violations;

    /**
     * @var string[]
     */
    private $suggestions;

    /**
     * @var User
     */
    private $user;

    /**
     * @var bool
     */
    private $valid;

    /**
     * Constructor.
     *
     * @param ConstraintViolationListInterface $violations
     * @param string[]                         $suggestions
     * @param User                             $user
     */
    public function __construct(ConstraintViolationListInterface $violations = null, array $suggestions = [], User $user = null)
    {
        $this->violations  = $violations;
        $this->suggestions = $suggestions;
        $this->user        = $user;
        $this->valid       = count($violations) === 0;
    }

    /**
     * @return ConstraintViolationListInterface|null
     */
    public function getViolations(): ?ConstraintViolationListInterface
    {
        return $this->violations;
    }

    /**
     * @return string[]
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }
}
