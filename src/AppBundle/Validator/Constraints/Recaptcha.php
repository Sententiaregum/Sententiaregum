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

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for captchas.
 *
 * @author Benjamin Bieler <benjaminbieler2014@gmail.com>
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
