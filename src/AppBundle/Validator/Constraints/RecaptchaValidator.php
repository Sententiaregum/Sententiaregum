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

use ReCaptcha\ReCaptcha as CaptchaValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class RecaptchaValidator.
 *
 * @author Benjamin Bieler <ben@benbieler.com>
 */
class RecaptchaValidator extends ConstraintValidator
{
    /**
     * @var CaptchaValidator
     */
    private $validator;

    /**
     * @var string
     */
    private $siteUrl;

    /**
     * Constructor.
     *
     * @param CaptchaValidator $validator
     * @param string           $siteUrl
     */
    public function __construct(CaptchaValidator $validator, string $siteUrl)
    {
        $this->validator = $validator;
        $this->siteUrl   = $siteUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Recaptcha) {
            throw new UnexpectedTypeException($constraint, Recaptcha::class);
        }

        $check = $this->validator->verify($value, $this->siteUrl);

        if (!$check->isSuccess()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
