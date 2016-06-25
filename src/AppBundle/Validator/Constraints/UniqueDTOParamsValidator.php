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

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator which validates unique properties of multiple entities on a dto recursively.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UniqueDTOParamsValidator extends ConstraintValidator
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccess;

    /**
     * Constructor.
     *
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccess = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueDTOParams) {
            throw new UnexpectedTypeException($constraint, UniqueDTOParams::class);
        }

        if (!is_object($value)) {
            throw new UnexpectedTypeException($value, 'object');
        }

        $resolver = new OptionsResolver();
        $resolver->setRequired(['field', 'entity']);
        $resolver->setDefined(
            [
                'message',
                'propertyPath',
                'field',
                'entity',
                'manager',
            ]
        );

        $fieldConfig = $constraint->fieldConfig;
        if (0 === count($fieldConfig)) {
            throw new ConstraintDefinitionException(sprintf(
                'Field "%s" at constraint "%s" must not have no items!',
                'fieldConfig',
                UniqueDTOParams::class
            ));
        }

        $contextualValidator = $this->context->getValidator()->inContext($this->context);
        foreach ($constraint->fieldConfig as $configItem) {
            $item    = $resolver->resolve($configItem);
            $options = [];
            if (isset($item['message'])) {
                $options['message'] = $item['message'];
            }

            $options['field']  = $item['field'];
            $options['entity'] = $item['entity'];

            if (isset($item['manager'])) {
                $options['manager'] = $item['manager'];
            }

            $options['propertyPath'] = !isset($item['propertyPath']) ? $item['field'] : $item['propertyPath'];

            if (!$this->propertyAccess->isReadable($value, $options['propertyPath'])) {
                throw new ConstraintDefinitionException(sprintf(
                    'The property path "%s" on object "%s" is not readable!',
                    $options['propertyPath'],
                    get_class($value)
                ));
            }

            $propertyValue = $this->propertyAccess->getValue($value, $options['propertyPath']);
            $constraint    = new UniqueProperty($options);

            $contextualValidator->validate($propertyValue, $constraint);
        }
    }
}
