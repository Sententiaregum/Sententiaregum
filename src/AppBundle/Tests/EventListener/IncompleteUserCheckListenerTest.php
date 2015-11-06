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

namespace AppBundle\Tests\EventListener;

use AppBundle\EventListener\IncompleteUserCheckListener;
use AppBundle\Model\User\User;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;

class IncompleteUserCheckListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException
     * @expectedExceptionMessage BACKEND_AUTH_LOCKED
     */
    public function testUserIsLocked()
    {
        $user = new User();
        $user->setState(User::STATE_APPROVED);
        $user->lock();

        $hook = new IncompleteUserCheckListener();
        $hook->validateUserOnAuthentication(new OnAuthenticationEvent($user));
    }

    /**
     * @expectedException \Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException
     * @expectedExceptionMessage BACKEND_AUTH_NON_APPROVED
     */
    public function testUserIsNonApproved()
    {
        $user = new User();

        $hook = new IncompleteUserCheckListener();
        $hook->validateUserOnAuthentication(new OnAuthenticationEvent($user));
    }

    /**
     * @expectedException \Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException
     * @expectedExceptionMessage BACKEND_AUTH_NON_APPROVED
     */
    public function testUserIsNonApprovedAndLocked()
    {
        $user = new User();
        $user->lock();

        $hook = new IncompleteUserCheckListener();
        $hook->validateUserOnAuthentication(new OnAuthenticationEvent($user));
    }
}
