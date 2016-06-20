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

namespace AppBundle\Tests\Unit\Validator\Constraints;

use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Model\User\User;
use AppBundle\Validator\Constraints\UniqueProperty;
use AppBundle\Validator\Constraints\UniquePropertyValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;
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
            ->willReturn($this->returnValue(User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev', new PhpPasswordHasher())));

        $classMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(true);

        $classMetadata->isEmbeddedClass    = false;
        $classMetadata->isMappedSuperclass = false;

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
     * @expectedExceptionMessageRegExp /^Expected argument of type "scalar", "array" given$/
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
     * @expectedExceptionMessage Invalid field "invalid"!
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
     * @expectedExceptionMessage The configured field "test-field" must not be an embeddable, an association or an identifier!
     */
    public function testFieldIsAssociation()
    {
        $classMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasAssociation')
            ->willReturn(true);

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
            new UniqueProperty(['entity' => 'AnotherMapping:User', 'field' => 'test-field'])
        );
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage The given entity "AnotherMapping:User" must not be an embeddable or an abstract/superclass object!
     */
    public function testClassIsEmbeddable()
    {
        $classMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(true);

        $classMetadata->isEmbeddedClass    = true;
        $classMetadata->isMappedSuperclass = false;

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
            new UniqueProperty(['entity' => 'AnotherMapping:User', 'field' => 'test-field'])
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
            ->assertRaised();
    }
}
