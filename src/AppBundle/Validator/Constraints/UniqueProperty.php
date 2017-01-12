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
 * Validation constraint for unique properties.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
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
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return ['entity', 'field'];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'app.validator.unique_property';
    }
}
