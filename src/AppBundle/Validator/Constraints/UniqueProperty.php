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
 * Validation constraint for unique properties.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class UniqueProperty extends Constraint
{
    const NON_UNIQUE_PROPERTY = 'df6f84fdc2bb6710fbc6ac8d0b068407';

    /**
     * Entity manager alias. This value can be left empty.
     *
     * @var string
     */
    public $manager;

    /**
     * Alias of the entity (required).
     *
     * @var string
     */
    public $entity;

    /**
     * Field to be validated (required).
     *
     * @var string
     */
    public $field;

    /**
     * Error message.
     *
     * @var string
     */
    public $message = 'The property %property% of entity %entity% with value %value% is not unique!';

    /**
     * Optional property to be used as custom property path on validation failures.
     *
     * @var string
     */
    public $propertyPath;

    /**
     * Flag whether to suggest other values.
     *
     * @var bool
     */
    public $generateSuggestions = false;

    /**
     * Translation key of the suggestion message.
     *
     * @var string
     */
    public $suggestionMessage = 'There are the following suggested values for %field%: %suggestions%';

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['entity', 'field'];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'app.validator.unique_property';
    }
}
