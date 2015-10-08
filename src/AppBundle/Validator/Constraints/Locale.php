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
    public $message = 'Locale %locale% does not exist in locale list %locales%!';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'app.validator.locale';
    }
}
