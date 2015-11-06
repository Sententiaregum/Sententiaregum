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

namespace AppBundle\Tests\Validator\Constraints;

use AppBundle\Validator\Constraints\Locale;
use AppBundle\Validator\Constraints\LocaleValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ExecutionContextInterface as LegacyContext;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

class LocaleValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new LocaleValidator(['de', 'en']);
    }

    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    public function testInvalidLocale()
    {
        $locale = new Locale();

        $this->validator->validate('fr', $locale);

        $this->buildViolation('Locale %locale% does not exist in locale list %locales%!')
            ->setParameter('%locale%', 'fr')
            ->setParameter('%locales%', 'de, en')
            ->setInvalidValue('fr')
            ->assertRaised();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /Expected argument of type "Symfony\\Component\\Validator\\Context\\ExecutionContextInterface", ".*" given/
     */
    public function testInvalidContext()
    {
        $localeValidator = new LocaleValidator([]);
        $localeValidator->initialize($this->getMock(LegacyContext::class));

        $localeValidator->validate('de', new Locale());
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /Expected argument of type "AppBundle\\Validator\\Constraints\\Locale", ".*" given/
     */
    public function testInvalidConstraint()
    {
        $localeValidator = new LocaleValidator([]);
        $localeValidator->validate('value', $this->getMock(Constraint::class));
    }
}
