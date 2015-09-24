<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Validator\Constraints;

use AppBundle\Model\User\User;
use AppBundle\Validator\Constraints\UniqueProperty;
use AppBundle\Validator\Constraints\UniquePropertyValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContextInterface as LegacyExecutionContextInterface;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

class UniquePropertyValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        $repository = $this->getMock(ObjectRepository::class);
        $repository
            ->expects($this->any())
            ->method('findOneBy')
            ->with(['username' => 'Ma27'])
            ->will($this->returnValue(User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev')));

        $manager = $this->getMock(ObjectManager::class);
        $manager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $mockRegistry = $this->getMock(ManagerRegistry::class);
        $mockRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnValue($manager));

        return new UniquePropertyValidator($mockRegistry);
    }

    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /Expected argument of type "AppBundle\\Validator\\Constraints\\UniqueProperty", ".*" given/
     */
    public function testInvalidConstraint()
    {
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class));
        $propertyMock->validate('value', $this->getMock(Constraint::class));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /Expected argument of type "Symfony\\Component\\Validator\\Context\\ExecutionContextInterface", ".*" given/
     */
    public function testLegacyExecutionContext()
    {
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class));
        $propertyMock->initialize($this->getMock(LegacyExecutionContextInterface::class));

        $propertyMock->validate('value', new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username']));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /^Expected argument of type "scalar", "array" given$/
     */
    public function testValueMustBeString()
    {
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate([], new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username']));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage No such entity manager with alias "custom_manager"!
     */
    public function testInvalidManagerAlias()
    {
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            'test',
            new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username', 'manager' => 'custom_manager'])
        );
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage Cannot find entity manager for model "AnotherMapping:InvalidModel"!
     */
    public function testNoManagerForModel()
    {
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            'test',
            new UniqueProperty(['entity' => 'AnotherMapping:InvalidModel', 'field' => 'username'])
        );
    }

    public function testUniqueField()
    {
        $this->validator->validate(
            'Ma27',
            new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username', 'propertyPath' => 'custom'])
        );

        $this->buildViolation('The property %property% of entity %entity% with value %value% is not unique!')
            ->setParameter('%property%', 'username')
            ->setParameter('%entity%', 'TestMapping:User')
            ->setParameter('%value%', 'Ma27')
            ->atPath('property.path.custom')
            ->assertRaised();
    }
}
