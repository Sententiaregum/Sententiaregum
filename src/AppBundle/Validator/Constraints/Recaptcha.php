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

/**
 * Validation constraint for captchas.
 *
 * @author Benjamin Bieler <ben@benbieler.com>
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Recaptcha extends Constraint
{
    /**
     * Error message.
     *
     * @var string
     */
    public $message = 'Invalid captcha!';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'app.validator.recaptcha_key';
    }
}
