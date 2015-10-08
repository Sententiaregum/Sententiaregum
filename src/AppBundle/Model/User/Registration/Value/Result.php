<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User\Registration\Value;

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
    public function __construct(ConstraintViolationListInterface $violations = null, array $suggestions = null, User $user = null)
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
