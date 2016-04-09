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

namespace AppBundle\Model\User\Value\Registration;

use AppBundle\Model\User\User;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Result object.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class Result
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
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @return string[]
     */
    public function getSuggestions()
    {
        return $this->suggestions;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }
}
