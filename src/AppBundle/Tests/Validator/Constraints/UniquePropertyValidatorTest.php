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

use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Model\User\User;
use AppBundle\Validator\Constraints\UniqueProperty;
use AppBundle\Validator\Constraints\UniquePropertyValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
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
            ->willReturn($this->returnValue(User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev')));

        $classMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(true);

        $manager = $this->getMock(ObjectManager::class);
        $manager
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);
        $manager
            ->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $mockRegistry = $this->getMock(ManagerRegistry::class);
        $mockRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($manager);

        $suggestor = $this->getMock(SuggestorInterface::class);
        $suggestor
            ->expects($this->any())
            ->method('getPossibleSuggestions')
            ->willReturn(['Ma.27']);

        return new UniquePropertyValidator($mockRegistry, $suggestor);
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
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class), $this->getMock(SuggestorInterface::class));
        $propertyMock->validate('value', $this->getMock(Constraint::class));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /Expected argument of type "Symfony\\Component\\Validator\\Context\\ExecutionContextInterface", ".*" given/
     */
    public function testLegacyExecutionContext()
    {
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class), $this->getMock(SuggestorInterface::class));
        $propertyMock->initialize($this->getMock(LegacyExecutionContextInterface::class));

        $propertyMock->validate('value', new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username']));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /^Expected argument of type "scalar or object", "array" given$/
     */
    public function testValueMustBeString()
    {
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class), $this->getMock(SuggestorInterface::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate([], new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username']));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage No such entity manager with alias "custom_manager"!
     */
    public function testInvalidManagerAlias()
    {
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class), $this->getMock(SuggestorInterface::class));
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
        $propertyMock = new UniquePropertyValidator($this->getMock(ManagerRegistry::class), $this->getMock(SuggestorInterface::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            'test',
            new UniqueProperty(['entity' => 'AnotherMapping:InvalidModel', 'field' => 'username'])
        );
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage Entity "AnotherMapping:User" has no field "invalid"!
     */
    public function testInvalidProperty()
    {
        $classMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(false);

        $manager = $this->getMock(ObjectManager::class);
        $manager
            ->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $mockRegistry = $this->getMock(ManagerRegistry::class);
        $mockRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($manager);

        $propertyMock = new UniquePropertyValidator($mockRegistry, $this->getMock(SuggestorInterface::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            'test',
            new UniqueProperty(['entity' => 'AnotherMapping:User', 'field' => 'invalid'])
        );
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage Entity "AnotherMapping:User" has no embeddable "foo"!
     */
    public function testInvalidEmbeddable()
    {
        $classMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(false);

        $manager = $this->getMock(ObjectManager::class);
        $manager
            ->expects($this->any())
            ->method('getClassMetadata')
            ->with('AnotherMapping:User')
            ->willReturn($classMetadata);

        $mockRegistry = $this->getMock(ManagerRegistry::class);
        $mockRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($manager);

        $propertyMock = new UniquePropertyValidator($mockRegistry, $this->getMock(SuggestorInterface::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            'test',
            new UniqueProperty(['entity' => 'AnotherMapping:User', 'field' => 'foo.bar'])
        );
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage Embeddable "foo" on entity "AnotherMapping:User" has no field "bar"!
     */
    public function testInvalidValueObjectProperty()
    {
        $classMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(false);

        $classMetadata->embeddedClasses = ['foo' => ['class' => 'EmbeddedClass']]; // adding mocked field

        $manager = $this->getMock(ObjectManager::class);
        $manager
            ->expects($this->at(0))
            ->method('getClassMetadata')
            ->with('AnotherMapping:User')
            ->willReturn($classMetadata);

        $embeddedMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $embeddedMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(false);

        $manager
            ->expects($this->at(1))
            ->method('getClassMetadata')
            ->with('EmbeddedClass')
            ->willReturn($embeddedMetadata);

        $mockRegistry = $this->getMock(ManagerRegistry::class);
        $mockRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($manager);

        $propertyMock = new UniquePropertyValidator($mockRegistry, $this->getMock(SuggestorInterface::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            'test',
            new UniqueProperty(['entity' => 'AnotherMapping:User', 'field' => 'foo.bar'])
        );
    }

    public function testReInitializeRelatedObject()
    {
        $stdClass = new \stdClass();

        $classMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(false);

        $classMetadata
            ->expects($this->any())
            ->method('hasAssociation')
            ->with('foo')
            ->willReturn(true);

        $manager = $this->getMock(ObjectManager::class);
        $manager
            ->expects($this->at(0))
            ->method('getClassMetadata')
            ->with('AnotherMapping:User')
            ->willReturn($classMetadata);

        $manager
            ->expects($this->once())
            ->method('initializeObject')
            ->with($stdClass);

        $repository = $this->getMock(ObjectRepository::class);
        $manager
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);

        $mockRegistry = $this->getMock(ManagerRegistry::class);
        $mockRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($manager);

        $propertyMock = new UniquePropertyValidator($mockRegistry, $this->getMock(SuggestorInterface::class));
        $propertyMock->initialize($this->getMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            $stdClass,
            new UniqueProperty(['entity' => 'AnotherMapping:User', 'field' => 'foo'])
        );
    }

    public function testUniqueField()
    {
        $this->validator->validate(
            'Ma27',
            new UniqueProperty(
                [
                    'entity'              => 'TestMapping:User',
                    'field'               => 'username',
                    'propertyPath'        => 'custom',
                    'generateSuggestions' => true,
                    'suggestionMessage'   => '%suggestions%',
                ]
            )
        );

        $this
            ->buildViolation('The property %property% of entity %entity% with value %value% is not unique!')
            ->setParameter('%property%', 'username')
            ->setParameter('%entity%', 'TestMapping:User')
            ->setParameter('%value%', 'Ma27')
            ->atPath('property.path.custom')
            ->setInvalidValue('Ma27')
            ->setCode(UniqueProperty::NON_UNIQUE_PROPERTY)
            ->buildNextViolation('%suggestions%')
            ->setParameter('%property%', 'username')
            ->setParameter('%entity%', 'TestMapping:User')
            ->setParameter('%value%', 'Ma27')
            ->setParameter('%suggestions%', 'Ma.27')
            ->atPath('property.path.custom')
            ->assertRaised();
    }
}
