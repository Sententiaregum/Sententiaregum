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

use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\SuggestorInterface;
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
     * @var SuggestorInterface
     */
    private $suggestor;

    /**
     * Constructor.
     *
     * @param ManagerRegistry    $managerRegistry
     * @param SuggestorInterface $suggestor
     */
    public function __construct(ManagerRegistry $managerRegistry, SuggestorInterface $suggestor)
    {
        $this->managerRegistry = $managerRegistry;
        $this->suggestor       = $suggestor;
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
            && !is_object($value) // allowing objects as one-to-one values should be validated, too
        ) {
            throw new UnexpectedTypeException($value, 'scalar or object');
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

        if (!$metadata->hasField($field) && !$metadata->hasAssociation($field)) {
            if (false !== strpos($field, '.')) {
                list($embeddable, $embeddedField) = explode('.', $field, 2);

                if (isset($metadata->embeddedClasses[$embeddable])) {
                    /** @var \Doctrine\ORM\Mapping\ClassMetadata $embeddableMetadata */
                    $embeddableMetadata = $manager->getClassMetadata($metadata->embeddedClasses[$embeddable]['class']);

                    if (!$embeddableMetadata->hasField($embeddedField)) {
                        throw new ConstraintDefinitionException(sprintf(
                            'Embeddable "%s" on entity "%s" has no field "%s"!',
                            $embeddable,
                            $entityAlias,
                            $embeddedField
                        ));
                    }
                } else {
                    throw new ConstraintDefinitionException(sprintf('Entity "%s" has no embeddable "%s"!', $entityAlias, $embeddable));
                }
            } else {
                throw new ConstraintDefinitionException(sprintf('Entity "%s" has no field "%s"!', $entityAlias, $field));
            }
        }

        if ($metadata->hasAssociation($field)) {
            $manager->initializeObject($value);
        }

        /** @var \Doctrine\Common\Persistence\ObjectRepository $repository */
        $repository = $manager->getRepository($entityAlias);
        $query      = [$field => $value];
        $search     = $repository->findOneBy($query);

        if (!empty($search)) {
            $notUniqueBuilder = $this->buildBasicViolationByMessage(
                $constraint->message,
                $field,
                $entityAlias,
                $value,
                $constraint->propertyPath
            );

            $notUniqueBuilder
                ->setInvalidValue($value)
                ->setCode(UniqueProperty::NON_UNIQUE_PROPERTY)
                ->addViolation();

            if ($constraint->generateSuggestions
                && !empty($suggestions = $this->suggestor->getPossibleSuggestions($value))
            ) {
                $suggestionBuilder = $this->buildBasicViolationByMessage(
                    $constraint->suggestionMessage,
                    $field,
                    $entityAlias,
                    $value,
                    $constraint->propertyPath
                );

                $suggestionBuilder
                    ->setParameter('%suggestions%', implode(', ', $suggestions))
                    ->addViolation();
            }
        }
    }

    /**
     * Creates the basic constraint violation builder.
     *
     * @param string $message
     * @param string $property
     * @param string $entity
     * @param mixed  $value
     * @param string $propertyPath
     *
     * @return \Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface
     */
    private function buildBasicViolationByMessage($message, $property, $entity, $value, $propertyPath = null)
    {
        /** @var ExecutionContextInterface $context */
        $context = $this->context;

        $violationBuilder = $context
            ->buildViolation($message)
            ->setParameter('%property%', $property)
            ->setParameter('%entity%', $entity)
            ->setParameter('%value%', $value);

        if (null !== $path = $propertyPath) {
            $violationBuilder->atPath($path);
        }

        return $violationBuilder;
    }
}
