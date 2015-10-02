<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Model\User\Registration;

use AppBundle\Event\MailerEvent;
use AppBundle\Model\User\Registration\DTO\CreateUserDTO;
use AppBundle\Model\User\Registration\Generator\ActivationKeyCodeGeneratorInterface;
use AppBundle\Model\User\Registration\TwoStepRegistrationProcess;
use AppBundle\Model\User\Role;
use AppBundle\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TwoStepRegistrationProcessTest extends \PHPUnit_Framework_TestCase
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

        $userManager = new TwoStepRegistrationProcess(
            $this->getMock(EntityManagerInterface::class),
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

        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist');

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->at(1))
            ->method('findOneBy')
            ->with(['username' => 'Ma27'])
            ->will($this->returnValue(User::create('Ma27', '123456', 'Ma27@sententiaregum.dev')));

        $repository
            ->expects($this->at(0))
            ->method('findOneBy')
            ->with(['role' => 'ROLE_USER'])
            ->will($this->returnValue(new Role('ROLE_USER')));

        $entityManager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));

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

        $userManager = new TwoStepRegistrationProcess(
            $entityManager,
            $generator,
            $this->getMock(ValidatorInterface::class),
            $dispatcher
        );

        $result = $userManager->registration($dto);
        $this->assertInstanceOf(User::class, $result);
    }
}
