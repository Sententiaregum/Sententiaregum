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

use AppBundle\Model\User\DTO\ActivateAccountDTO;
use AppBundle\Model\User\Handler\ActivateAccountHandler;
use AppBundle\Model\User\Role;
use AppBundle\Model\User\RoleReadRepositoryInterface;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserReadRepositoryInterface;
use AppBundle\Model\User\UserWriteRepositoryInterface;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class ActivateAccountHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \AppBundle\Exception\UserActivationException
     */
    public function testInvalidData()
    {
        $writeRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $writeRepository
            ->expects($this->never())
            ->method('save');

        $readRepository = $this->createMock(UserReadRepositoryInterface::class);
        $readRepository
            ->expects($this->once())
            ->method('findUserByUsernameAndActivationKey')
            ->with('Ma27', 'key');

        $roleRepository = $this->createMock(RoleReadRepositoryInterface::class);
        $roleRepository
            ->expects($this->never())
            ->method('determineDefaultRole');

        $handler = new ActivateAccountHandler($readRepository, $writeRepository, $roleRepository);
        $dto     = new ActivateAccountDTO();

        $dto->username      = 'Ma27';
        $dto->activationKey = 'key';

        $handler($dto);
    }

    /**
     * @expectedException \AppBundle\Exception\UserActivationException
     */
    public function testExpiredActivation()
    {
        $key  = md5(uniqid());
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());

        // hack into the activation model
        // and modify the activation date in order to
        // test scenarios with expired activations
        $r = new \ReflectionClass($user);
        $p = $r->getProperty('pendingActivation');
        $p->setAccessible(true);
        /** @var \AppBundle\Model\User\PendingActivation $v */
        $v  = $p->getValue($user);
        $r2 = new \ReflectionClass($v);
        $p2 = $r2->getProperty('activationDate');
        $p2->setAccessible(true);
        $p2->setValue($v, new \DateTime('-5 hours'));
        $p->setValue($user, $v);

        $readRepository = $this->createMock(UserReadRepositoryInterface::class);
        $readRepository
            ->expects($this->once())
            ->method('findUserByUsernameAndActivationKey')
            ->with('Ma27', $key)
            ->willReturn($user);

        $roleRepository = $this->createMock(RoleReadRepositoryInterface::class);
        $roleRepository
            ->expects($this->never())
            ->method('determineDefaultRole');

        $writeRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $writeRepository
            ->expects($this->never())
            ->method('save');

        $handler = new ActivateAccountHandler($readRepository, $writeRepository, $roleRepository);
        $dto     = new ActivateAccountDTO();

        $dto->username      = 'Ma27';
        $dto->activationKey = $key;

        $handler($dto);
    }

    public function testActivateAccount()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->performStateTransition(User::STATE_NEW);
        $user->storeUniqueActivationKeyForNonApprovedUser('key');

        $writeRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $writeRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $readRepository = $this->createMock(UserReadRepositoryInterface::class);
        $readRepository
            ->expects($this->once())
            ->method('findUserByUsernameAndActivationKey')
            ->with('Ma27', 'key')
            ->willReturn($user);

        $role = new Role('ROLE_USER');

        $roleRepository = $this->createMock(RoleReadRepositoryInterface::class);
        $roleRepository
            ->expects($this->once())
            ->method('determineDefaultRole')
            ->willReturn($role);

        $handler = new ActivateAccountHandler($readRepository, $writeRepository, $roleRepository);
        $dto     = new ActivateAccountDTO();

        $dto->username      = 'Ma27';
        $dto->activationKey = 'key';

        $handler($dto);

        $this->assertTrue($user->hasRole($role));
        $this->assertSame(User::STATE_APPROVED, $user->getState());
        $this->assertNull($user->getPendingActivation());
    }
}
