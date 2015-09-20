<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
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
