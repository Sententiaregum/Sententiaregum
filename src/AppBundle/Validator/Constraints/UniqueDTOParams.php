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
 * Constraint to validate multiple dto params.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueDTOParams extends Constraint
{
    /**
     * @var string[]
     */
    public $fieldConfig = [];

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return ['fieldConfig'];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'app.validator.unique_dto_params';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
