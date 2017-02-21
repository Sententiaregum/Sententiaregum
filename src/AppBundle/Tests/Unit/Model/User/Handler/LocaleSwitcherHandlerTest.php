<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Tests\Unit\Model\User\Handler;

use AppBundle\Model\User\DTO\LocaleSwitcherDTO;
use AppBundle\Model\User\Handler\LocaleSwitcherHandler;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserWriteRepositoryInterface;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class LocaleSwitcherHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testUserWithoutLocaleChange(): void
    {
        $userRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $userRepository
            ->expects($this->never())
            ->method('save');

        $handler = new LocaleSwitcherHandler($userRepository);
        $dto     = new LocaleSwitcherDTO();

        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->modifyUserLocale('de');

        $dto->locale = 'de';
        $dto->user   = $user;

        $handler($dto);
        $this->assertSame('de', $user->getLocale());
    }

    public function testUpdate(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());

        $userRepository = $this->createMock(UserWriteRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $handler = new LocaleSwitcherHandler($userRepository);
        $dto     = new LocaleSwitcherDTO();

        $this->assertSame('en', $user->getLocale());

        $dto->locale = 'de';
        $dto->user   = $user;

        $handler($dto);

        $this->assertSame('de', $user->getLocale());
    }
}
