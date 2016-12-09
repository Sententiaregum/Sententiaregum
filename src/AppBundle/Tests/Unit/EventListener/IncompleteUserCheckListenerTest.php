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

namespace AppBundle\Tests\Unit\EventListener;

use AppBundle\EventListener\IncompleteUserCheckListener;
use AppBundle\Model\User\Provider\BlockedAccountReadInterface;
use AppBundle\Model\User\User;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class IncompleteUserCheckListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException
     * @expectedExceptionMessage BACKEND_AUTH_LOCKED
     */
    public function testUserIsLocked()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->performStateTransition(User::STATE_APPROVED);
        $user->performStateTransition(User::STATE_LOCKED);

        $hook = new IncompleteUserCheckListener($this->createMock(BlockedAccountReadInterface::class));
        $hook->validateUserOnAuthentication(new OnAuthenticationEvent($user));
    }

    /**
     * @expectedException \Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException
     * @expectedExceptionMessage BACKEND_AUTH_NON_APPROVED
     */
    public function testUserIsNonApproved()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());

        $hook = new IncompleteUserCheckListener($this->createMock(BlockedAccountReadInterface::class));
        $hook->validateUserOnAuthentication(new OnAuthenticationEvent($user));
    }

    /**
     * @expectedException \Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException
     * @expectedExceptionMessage BACKEND_AUTH_BLOCKED
     */
    public function testAccountIsTemporaryBlocked()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->performStateTransition(User::STATE_APPROVED);

        $provider = $this->createMock(BlockedAccountReadInterface::class);
        $provider
            ->expects($this->once())
            ->method('isAccountTemporaryBlocked')
            ->with($user->getId())
            ->willReturn(true);

        $hook = new IncompleteUserCheckListener($provider);
        $hook->validateUserOnAuthentication(new OnAuthenticationEvent($user));
    }
}
