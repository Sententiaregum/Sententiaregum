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

namespace AppBundle\Tests\Validator\Constraints;

use AppBundle\Validator\Constraints\UniqueDTOParams;
use AppBundle\Validator\Constraints\UniqueDTOParamsValidator;
use AppBundle\Validator\Constraints\UniqueProperty;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

class UniqueDTOParamsValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new UniqueDTOParamsValidator(new PropertyAccessor());
    }

    protected function createContext()
    {
        $context = parent::createContext();

        $validator           = $context->getValidator();
        /** @var \PHPUnit_Framework_MockObject_MockObject $contextualValidator */
        $contextualValidator = $validator->inContext($context);

        $contextualValidator
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnCallback(
                function ($value, UniqueProperty $constraint) use ($context) {
                    $context->buildViolation($constraint->message)
                        ->setParameter('%property%', $constraint->field)
                        ->setParameter('%entity%', $constraint->entity)
                        ->setParameter('%value%', $value)
                        ->setCode(UniqueProperty::NON_UNIQUE_PROPERTY)
                        ->addViolation();
                }
            ));

        return $context;
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /Expected argument of type "AppBundle\\Validator\\Constraints\\UniqueDTOParams", ".*" given/
     */
    public function testInvalidConstraint()
    {
        $this->validator->validate(new \stdClass(), $this->getMock(Constraint::class));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "object", "string" given
     */
    public function testNotAnObject()
    {
        $this->validator->validate('foo-bar', new UniqueDTOParams(['fieldConfig' => []]));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage Field "fieldConfig" at constraint "AppBundle\Validator\Constraints\UniqueDTOParams" must not have no items!
     */
    public function testEmptyFieldConfig()
    {
        $this->validator->validate(new \stdClass(), new UniqueDTOParams(['fieldConfig' => []]));
    }

    public function testValidateMultiplePropertiesAtDTO()
    {
        $object      = new \stdClass();
        $object->foo = 'bar';

        $constraint = new UniqueDTOParams([
            'fieldConfig' => [
                [
                    'field'   => 'foo',
                    'entity'  => 'AnotherMapping:User',
                    'message' => 'Invalid property!',
                    'manager' => 'default',
                ],
            ],
        ]);

        $this->validator->validate($object, $constraint);

        $this->buildViolation('Invalid property!')
            ->setParameter('%property%', 'foo')
            ->setParameter('%entity%', 'AnotherMapping:User')
            ->setParameter('%value%', 'bar')
            ->setCode(UniqueProperty::NON_UNIQUE_PROPERTY)
            ->assertRaised();
    }

    public function testValidateRecursiveProperties()
    {
        $object       = new \stdClass();
        $object1      = new \stdClass();
        $object1->bar = 'baz';
        $object->foo  = $object1;

        $constraint = new UniqueDTOParams([
            'fieldConfig' => [
                [
                    'field'   => 'foo.bar',
                    'entity'  => 'AnotherMapping:User',
                    'message' => 'Invalid property!',
                ],
            ],
        ]);

        $this->validator->validate($object, $constraint);

        $this->buildViolation('Invalid property!')
            ->setParameter('%property%', 'foo.bar')
            ->setParameter('%entity%', 'AnotherMapping:User')
            ->setParameter('%value%', 'baz')
            ->setCode(UniqueProperty::NON_UNIQUE_PROPERTY)
            ->assertRaised();
    }
}
