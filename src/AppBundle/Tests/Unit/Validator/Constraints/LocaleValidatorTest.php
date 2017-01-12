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

namespace AppBundle\Tests\Unit\Validator\Constraints;

use AppBundle\Validator\Constraints\Locale;
use AppBundle\Validator\Constraints\LocaleValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class LocaleValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createValidator()
    {
        return new LocaleValidator(['de', 'en']);
    }

    /**
     * {@inheritdoc}
     */
    protected function createContext()
    {
        $context = parent::createContext();

        $validator           = $context->getValidator();
        /** @var \PHPUnit_Framework_MockObject_MockObject $contextualValidator */
        $contextualValidator = $validator->inContext($context);

        $contextualValidator
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnCallback(
                function ($value, Choice $choice) use ($context) {
                    if (!in_array($value, $choice->choices, true)) {
                        $context->buildViolation($choice->message)
                            ->setParameter('{{ value }}', $value)
                            ->setCode(Choice::NO_SUCH_CHOICE_ERROR)
                            ->addViolation();
                    }
                }
            ));

        return $context;
    }

    public function testInvalidLocale(): void
    {
        $locale = new Locale();
        $this->validator->validate('fr', $locale);
        $this->buildViolation('Locale {{ value }} is invalid!')
            ->setParameter('{{ value }}', 'fr')
            ->setCode(Choice::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /Expected argument of type "AppBundle\\Validator\\Constraints\\Locale", ".*" given/
     */
    public function testInvalidConstraint(): void
    {
        $localeValidator = new LocaleValidator([]);
        $localeValidator->validate('value', $this->createMock(Constraint::class));
    }
}
