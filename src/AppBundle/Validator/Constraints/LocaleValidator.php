<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Validator\Constraints;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator which can be used in order to verify the user locale.
 * It is actually a choice validator that contains the locale config internally.
 *
 * @DI\Validator("app.validator.locale")
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
     *
     * @DI\InjectParams({"allowedLocales" = @DI\Inject("%app.locales%")})
     */
    public function __construct(array $allowedLocales)
    {
        $this->allowedLocales = $allowedLocales;
    }

    /**
     * Validates the value
     *
     * @param string $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Locale) {
            throw new UnexpectedTypeException($constraint, Locale::class);
        }

        $context = $this->context;
        if (!$context instanceof ExecutionContextInterface) {
            throw new UnexpectedTypeException($context, ExecutionContextInterface::class);
        }

        if (!in_array($value, $this->allowedLocales)) {
            $context->buildViolation($constraint->message)
                ->setParameter('%locale%', $value)
                ->setParameter('%locales%', self::getLocalesAsString($this->allowedLocales))
                ->setInvalidValue($value)
                ->addViolation();
        }
    }

    /**
     * Converts the allowed locales to string
     *
     * @param string[] $allowedLocales
     *
     * @return string
     */
    private static function getLocalesAsString(array $allowedLocales)
    {
        return implode(', ', $allowedLocales);
    }
}
