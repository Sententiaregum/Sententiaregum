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

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator which validates a unique constraints.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UniquePropertyValidator extends ConstraintValidator
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * Validates whether the given value is unique when using it as a specific field on a specific model.
     *
     * @param string|object $value
     * @param Constraint    $constraint
     *
     * @throws UnexpectedTypeException       If the constraint is no UniqueProperty constraint.
     * @throws UnexpectedTypeException       If the ExecutionContext is invalid (to be removed when upgrading to 3.0, just used in order to verify the correct context).
     * @throws UnexpectedTypeException       If the value is not a scalar value or has a __toString() interceptor.
     * @throws ConstraintDefinitionException If the manager alias could not be loaded or there's no manager for a specific class.
     * @throws ConstraintDefinitionException If the entity field cannot be found.
     * @throws ConstraintDefinitionException If the entity field is an invalid mapping (e.g. an association or an identifier)
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var UniqueProperty $constraint */
        if (!$constraint instanceof UniqueProperty) {
            throw new UnexpectedTypeException($constraint, UniqueProperty::class);
        }

        /** @var ExecutionContextInterface $context */
        $context = $this->context;
        if (!$context instanceof ExecutionContextInterface) {
            throw new UnexpectedTypeException($context, ExecutionContextInterface::class);
        }

        if (!is_scalar($value) && (!is_object($value) && !method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        $entityAlias = $constraint->entity;

        if (null !== $managerAlias = $constraint->manager) {
            $manager = $this->managerRegistry->getManager($managerAlias);

            if (!$manager) {
                throw new ConstraintDefinitionException(sprintf('No such entity manager with alias "%s"!', $managerAlias));
            }
        } else {
            $manager = $this->managerRegistry->getManagerForClass($entityAlias);

            if (!$manager) {
                throw new ConstraintDefinitionException(sprintf('Cannot find entity manager for model "%s"!', $entityAlias));
            }
        }

        $field = $constraint->field;
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $manager->getClassMetadata($entityAlias);

        if ($metadata->hasAssociation($field)
            || isset($metadata->embeddedClasses[$field])
            || $metadata->isIdentifier($field)
        ) {
            throw new ConstraintDefinitionException(sprintf(
                'The configured field "%s" must not be an embeddable, an association or an identifier!',
                $field
            ));
        } elseif (!$metadata->hasField($field)) {
            throw new ConstraintDefinitionException(sprintf(
                'Invalid field "%s"!',
                $field
            ));
        }

        if ($metadata->isEmbeddedClass
            || $metadata->isMappedSuperclass
            || (($reflection = $metadata->getReflectionClass()) && $reflection->isAbstract())
        ) {
            throw new ConstraintDefinitionException(sprintf(
                'The given entity "%s" must not be an embeddable or an abstract/superclass object!',
                $entityAlias
            ));
        }

        /** @var \Doctrine\Common\Persistence\ObjectRepository $repository */
        $repository = $manager->getRepository($entityAlias);
        $query      = [$field => $value];
        $search     = $repository->findOneBy($query);

        if (!empty($search)) {
            $context
                ->buildViolation($constraint->message)
                ->setParameter('%property%', $field)
                ->setParameter('%entity%', $entityAlias)
                ->setParameter('%value%', $value)
                ->setInvalidValue($value)
                ->setCode(UniqueProperty::NON_UNIQUE_PROPERTY)
                ->atPath($constraint->propertyPath)
                ->addViolation();
        }
    }
}
