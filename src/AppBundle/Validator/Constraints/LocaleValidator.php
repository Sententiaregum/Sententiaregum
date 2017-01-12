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

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator which can be used in order to verify the user locale.
 * It is actually a choice validator that contains the locale config internally.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class LocaleValidator extends ConstraintValidator
{
    /**
     * @var string[]
     */
    private $allowedLocales;

    /**
     * Constructor.
     *
     * @param string[] $allowedLocales
     */
    public function __construct(array $allowedLocales)
    {
        $this->allowedLocales = $allowedLocales;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Locale) {
            throw new UnexpectedTypeException($constraint, Locale::class);
        }

        $validator     = $this->context->getValidator();
        $choiceOptions = [
            'strict'  => true,
            'choices' => $this->allowedLocales,
            'message' => $constraint->message,
        ];

        $validator
            ->inContext($this->context)
            ->validate($value, new Choice($choiceOptions));
    }
}
