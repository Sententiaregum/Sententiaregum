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

namespace AppBundle\Tests\Unit\Model\User\Handler;

use AppBundle\Model\Core\Provider\NotificatorInterface;
use AppBundle\Model\User\DTO\CreateUserDTO;
use AppBundle\Model\User\Handler\CreateUserHandler;
use AppBundle\Model\User\UserWriteRepositoryInterface;
use AppBundle\Model\User\Util\ActivationKeyCode\ActivationKeyCodeGenerator;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateUser(): void
    {
        $notificator = $this->createMock(NotificatorInterface::class);
        $notificator
            ->expects($this->once())
            ->method('publishNotification');

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $hasher     = new PhpPasswordHasher();
        $generator  = new ActivationKeyCodeGenerator();
        $repository = $this->createMock(UserWriteRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('save');

        $handler = new CreateUserHandler($repository, $hasher, $generator, $validator);
        $dto     = new CreateUserDTO();

        $handler->setNotificator($notificator);

        $dto->username = 'Ma27';
        $dto->password = '123456';
        $dto->email    = 'Ma27@sententiaregum.dev';
        $dto->locale   = 'de';

        $this->assertNull($dto->user);

        $handler($dto);

        $this->assertNotNull($dto->user);
        $user = $dto->user;

        $this->assertSame($user->getUsername(), 'Ma27');
        $this->assertTrue($hasher->compareWith($user->getPassword(), '123456'));
        $this->assertSame($user->getEmail(), 'Ma27@sententiaregum.dev');
        $this->assertSame($user->getLocale(), 'de');
        $this->assertCount(0, $user->getRoles());
    }

    /**
     * @expectedException \OverflowException
     * @expectedExceptionMessage Cannot generate activation key!
     */
    public function testGenerationFailure(): void
    {
        $notificator = $this->createMock(NotificatorInterface::class);
        $notificator
            ->expects($this->never())
            ->method('publishNotification');

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->any())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([new ConstraintViolation('Property non-unique!', 'Property non-unique!', [], 'root', 'key', 'key')]));

        $hasher     = new PhpPasswordHasher();
        $generator  = new ActivationKeyCodeGenerator();
        $repository = $this->createMock(UserWriteRepositoryInterface::class);
        $repository
            ->expects($this->never())
            ->method('save');

        $handler = new CreateUserHandler($repository, $hasher, $generator, $validator);
        $dto     = new CreateUserDTO();

        $handler->setNotificator($notificator);

        $dto->username = 'Ma27';
        $dto->password = '123456';
        $dto->email    = 'Ma27@sententiaregum.dev';
        $dto->locale   = 'de';

        $handler($dto);
    }
}
