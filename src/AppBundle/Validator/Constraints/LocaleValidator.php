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

namespace AppBundle\Validator\Constraints;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator which can be used in order to verify the user locale.
 * It is actually a choice validator that contains the locale config internally.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Locale) {
            throw new UnexpectedTypeException($constraint, Locale::class);
        }

        /** @var ExecutionContextInterface $context */
        $context = $this->context;
        if (!$context instanceof ExecutionContextInterface) {
            throw new UnexpectedTypeException($context, ExecutionContextInterface::class);
        }

        $validator     = $context->getValidator();
        $choiceOptions = [
            'strict'  => true,
            'choices' => array_keys($this->allowedLocales),
            'message' => $constraint->message,
        ];

        $validator
            ->inContext($context)
            ->validate($value, new Choice($choiceOptions));
    }
}
