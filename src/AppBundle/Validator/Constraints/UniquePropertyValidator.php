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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
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
     * {@inheritdoc}
     *
     * @throws UnexpectedTypeException       If the constraint is no UniqueProperty constraint.
     * @throws UnexpectedTypeException       If the value is not a scalar value or has a __toString() interceptor.
     * @throws ConstraintDefinitionException If the manager alias could not be loaded or there's no manager for a specific class.
     * @throws ConstraintDefinitionException If an @see{ORMException} caused by misconfigured fields.
     */
    public function validate($value, Constraint $constraint): void
    {
        /** @var UniqueProperty $constraint */
        if (!$constraint instanceof UniqueProperty) {
            throw new UnexpectedTypeException($constraint, UniqueProperty::class);
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

        try {
            // NOTE: in cases like misconfigured fields an ORMException will be thrown.
            // In order to ease debugging, this exception will be catched and a ConstraintDefinitionException will
            // be thrown.

            /** @var \Doctrine\ORM\EntityRepository $repository */
            $repository = $manager->getRepository($entityAlias);
            $search     = $repository->findOneBy([$field => $value]);
        } catch (ORMException $e) {
            throw new ConstraintDefinitionException(sprintf(
                'During the validation whether the given property is unique or not, doctrine threw an exception with the following message: "%s". Did you misconfigure any parameters such as the field or entity name?',
                $e->getMessage()
            ), 0, $e);
        }

        if (!empty($search)) {
            $this
                ->context
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
