<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Model\User;

use AppBundle\Event\MailerEvent;
use AppBundle\Model\User\Data\DTO\CreateUserDTO;
use AppBundle\Model\User\Generator\ActivationKeyCodeGeneratorInterface;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInvalidUser()
    {
        $dto = new CreateUserDTO();
        $dto->setUsername('Ma27');
        $dto->setPassword('123456');
        $dto->setEmail('Ma27@sententiaregum.dev');

        $validatorMock = $this->getMock(ValidatorInterface::class);
        $validatorMock
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(
                new ConstraintViolationList(
                    [new ConstraintViolation('Invalid username!', 'Invalid username!', [], null, 'username', 'Ma27')]
                )
            ));

        $userManager = new UserManager(
            User::class,
            $this->getMock(ManagerRegistry::class),
            $this->getMock(ActivationKeyCodeGeneratorInterface::class),
            $validatorMock,
            $this->getMock(EventDispatcherInterface::class)
        );

        $result = $userManager->registration($dto);
        $this->assertInstanceOf(ConstraintViolationList::class, $result);

        $this->assertCount(1, $result);
    }

    public function testCreateUser()
    {
        $dto = new CreateUserDTO();
        $dto->setUsername('Ma27');
        $dto->setPassword('123456');
        $dto->setEmail('Ma27@sententiaregum.dev');

        $entityManager = $this->getMock(ObjectManager::class);
        $entityManager
            ->expects($this->once())
            ->method('persist');

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $repository = $this->getMock(ObjectRepository::class);
        $repository
            ->expects($this->any())
            ->method('findOneBy')
            ->with(['username' => 'Ma27'])
            ->will($this->returnValue(User::create('Ma27', '123456', 'Ma27@sententiaregum.dev')));

        $entityManager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $managerRegistry = $this->getMock(ManagerRegistry::class);
        $managerRegistry
            ->expects($this->exactly(3))
            ->method('getManagerForClass')
            ->will($this->returnValue($entityManager));

        $generator = $this->getMock(ActivationKeyCodeGeneratorInterface::class);
        $generator
            ->expects($this->any())
            ->method('generate')
            ->with(255)
            ->will($this->returnValue(str_repeat('X', 255)));

        $dispatcher = $this->getMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(MailerEvent::EVENT_NAME);

        $userManager = new UserManager(
            User::class,
            $managerRegistry,
            $generator,
            $this->getMock(ValidatorInterface::class),
            $dispatcher
        );

        $result = $userManager->registration($dto);
        $this->assertInstanceOf(User::class, $result);
    }
}
