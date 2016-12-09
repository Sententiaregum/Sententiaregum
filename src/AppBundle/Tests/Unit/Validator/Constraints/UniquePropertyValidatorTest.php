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

namespace AppBundle\Tests\Unit\Validator\Constraints;

use AppBundle\Model\User\User;
use AppBundle\Validator\Constraints\UniqueProperty;
use AppBundle\Validator\Constraints\UniquePropertyValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class UniquePropertyValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->any())
            ->method('findOneBy')
            ->with(['username' => 'Ma27'])
            ->willReturn($this->returnValue(User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev', new PhpPasswordHasher())));

        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(true);

        $classMetadata->isEmbeddedClass    = false;
        $classMetadata->isMappedSuperclass = false;

        $manager = $this->createMock(ObjectManager::class);
        $manager
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);
        $manager
            ->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $mockRegistry = $this->createMock(ManagerRegistry::class);
        $mockRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($manager);

        return new UniquePropertyValidator($mockRegistry);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /Expected argument of type "AppBundle\\Validator\\Constraints\\UniqueProperty", ".*" given/
     */
    public function testInvalidConstraint()
    {
        $propertyMock = new UniquePropertyValidator($this->createMock(ManagerRegistry::class));
        $propertyMock->validate('value', $this->createMock(Constraint::class));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessageRegExp /^Expected argument of type "scalar", "array" given$/
     */
    public function testValueMustBeString()
    {
        $propertyMock = new UniquePropertyValidator($this->createMock(ManagerRegistry::class));
        $propertyMock->initialize($this->createMock(ExecutionContextInterface::class));

        $propertyMock->validate([], new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username']));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage No such entity manager with alias "custom_manager"!
     */
    public function testInvalidManagerAlias()
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->once())
            ->method('getManager');

        $propertyMock = new UniquePropertyValidator($registry);
        $propertyMock->initialize($this->createMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            'test',
            new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username', 'manager' => 'custom_manager'])
        );
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage Cannot find entity manager for model "TestMapping:User"!
     */
    public function testNoManagerForEntity()
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->once())
            ->method('getManagerForClass');

        $propertyMock = new UniquePropertyValidator($registry);
        $propertyMock->initialize($this->createMock(ExecutionContextInterface::class));

        $propertyMock->validate(
            'test',
            new UniqueProperty(['entity' => 'TestMapping:User', 'field' => 'username'])
        );
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage During the validation whether the given property is unique or not, doctrine threw an exception with the following message: "Unrecognized field: test-field". Did you misconfigure any parameters such as the field or entity name?
     */
    public function testFindOneByThrowsORMException()
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['test-field' => 'test'])
            ->willReturnCallback(function () {
                throw ORMException::unrecognizedField('test-field');
            });

        $manager = $this->createMock(ObjectManager::class);
        $manager
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($repository);

        $mockRegistry = $this->createMock(ManagerRegistry::class);
        $mockRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($manager);

        $propertyMock = new UniquePropertyValidator($mockRegistry);
        $propertyMock->initialize($this->createMock(ExecutionContextInterface::class));

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
                    'entity'       => 'TestMapping:User',
                    'field'        => 'username',
                    'propertyPath' => 'custom',
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
