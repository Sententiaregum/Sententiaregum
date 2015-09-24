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

use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator which validates a unique constraints.
 *
 * @DI\Validator("app.validator.unique_property")
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
     *
     * @DI\InjectParams({"managerRegistry" = @DI\Inject("doctrine")})
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * Validates whether the given value is unique when using it as a specific field on a specific model.
     *
     * @param string     $value
     * @param Constraint $constraint
     *
     * @throws UnexpectedTypeException       If the constraint is no UniqueProperty constraint.
     * @throws UnexpectedTypeException       If the ExecutionContext is invalid (to be removed when upgrading to 3.0, just used in order to verify the correct context).
     * @throws UnexpectedTypeException       If the value is not a scalar value or has a __toString() interceptor.
     * @throws ConstraintDefinitionException If the manager alias could not be loaded or there's no manager for a specific class
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

        if (!is_scalar($value)
            && (!is_object($value) && !method_exists($value, '__toString'))
        ) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        $value = (string) $value;

        if (null !== $managerAlias = $constraint->manager) {
            $manager = $this->managerRegistry->getManager($managerAlias);

            if (!$manager) {
                throw new ConstraintDefinitionException(sprintf('No such entity manager with alias "%s"!', $managerAlias));
            }
        } else {
            $entityAlias = $constraint->entity;
            $manager     = $this->managerRegistry->getManagerForClass($constraint->entity);

            if (!$manager) {
                throw new ConstraintDefinitionException(sprintf('Cannot find entity manager for model "%s"!', $entityAlias));
            }
        }

        /** @var \Doctrine\Common\Persistence\ObjectRepository $repository */
        $repository = $manager->getRepository($constraint->entity);
        $field      = $constraint->field;
        $query      = [$field => $value];
        $search     = $repository->findOneBy($query);

        if (!empty($search)) {
            $violationBuilder = $context
                ->buildViolation($constraint->message)
                ->setParameter('%property%', $field)
                ->setParameter('%entity%', $constraint->entity)
                ->setParameter('%value%', $value);

            if (null !== $path = $constraint->propertyPath) {
                $violationBuilder->atPath($path);
            }

            $violationBuilder->addViolation();
        }
    }
}
