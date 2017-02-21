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
 * Constraint which is responsible for the validation of the locales.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Locale extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Locale {{ value }} is invalid!';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'app.validator.locale';
    }
}
