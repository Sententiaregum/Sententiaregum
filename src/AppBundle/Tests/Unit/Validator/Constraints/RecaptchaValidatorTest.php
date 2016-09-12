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

namespace AppBundle\Tests\Unit\Validator\Constraints;

use AppBundle\Validator\Constraints\Locale;
use AppBundle\Validator\Constraints\Recaptcha as RecaptchaConstraint;
use AppBundle\Validator\Constraints\RecaptchaValidator;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

class RecaptchaValidatorTest extends AbstractConstraintValidatorTest
{
    public function createValidator()
    {
        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response->expects(self::any())
            ->method('isSuccess')
            ->willReturn(false);

        $recaptcha = $this->getMockBuilder(ReCaptcha::class)
            ->disableOriginalConstructor()
            ->getMock();

        $recaptcha->expects(self::any())
            ->method('verify')
            ->willReturn($response);

        return new RecaptchaValidator($recaptcha, 'http://sententiaregum.dev/');
    }

    public function testInvalidRecaptcha()
    {
        $this->validator->validate(
            'ivalid-hash',
            new RecaptchaConstraint()
        );

        $this->buildViolation('Invalid captcha!')->assertRaised();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testLocaleInsteadOfRecaptcha()
    {
        $this->validator->validate(
            'foo',
            new Locale()
        );
    }
}
