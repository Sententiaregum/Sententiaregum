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

use Symfony\Component\Validator\Constraint;

/**
 * Constraint which is responsible for the validation of the locales.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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
    public function validatedBy()
    {
        return 'app.validator.locale';
    }
}
