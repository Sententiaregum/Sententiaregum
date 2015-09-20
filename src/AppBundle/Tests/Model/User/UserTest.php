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

use AppBundle\Model\User\Role;
use AppBundle\Model\User\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testLockUnlock()
    {
        $user = new User();
        $this->assertFalse($user->isLocked());

        $user->lock();
        $this->assertTrue($user->isLocked());

        $user->unlock();
        $this->assertFalse($user->isLocked());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Approved users cannot have an activation key!
     */
    public function testSetActivationKeyOnApprovedUser()
    {
        $user = new User();
        $user->setState(User::STATE_APPROVED);

        $user->setActivationKey('any long activation key');
    }

    public function testRemoveActivationKey()
    {
        $user = new User();
        $user->setActivationKey('any long api key');
        $user->setState(User::STATE_APPROVED);

        $this->assertEmpty($user->getActivationKey());
        $this->assertSame(User::STATE_APPROVED, $user->getState());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot set empty activation key! Please call "removeActivationKey()" instead for the removal of the activation key!
     */
    public function testEmptyActivationKey()
    {
        $user = new User();
        $user->setActivationKey(null);
    }

    public function testFactory()
    {
        $user = User::create('Ma27', 'test', 'Ma27@sententiaregum.dev');
        $this->assertSame('Ma27', $user->getUsername());
        $this->assertSame('test', $user->getPassword());
        $this->assertSame('Ma27@sententiaregum.dev', $user->getEmail());
    }

    public function testRemoveApiKey()
    {
        $user = new User();

        $this->assertEmpty($user->getApiKey());
        $user->setApiKey('foo');
        $this->assertNotEmpty($user->getApiKey());
        $user->removeApiKey();
        $this->assertEmpty($user->getApiKey());
    }

    public function testFollower()
    {
        $user = new User();
        $user->setUsername('Ma27');

        $following = new User();
        $following->setUsername('benbieler');

        $user->addFollowing($following);
        $this->assertTrue($user->follows($following));

        $this->assertCount(1, $user->getFollowing());

        $user->removeFollowing($following);
        $this->assertFalse($user->follows($following));
        $this->assertCount(0, $user->getFollowing());
    }

    public function testRoles()
    {
        $user = new User();
        $role = new Role('ROLE_USER');

        $user->addRole($role);
        $this->assertTrue($user->hasRole($role));

        $this->assertCount(1, $user->getRoles());
        $user->removeRole($role);
        $this->assertFalse($user->hasRole($role));

        $this->assertCount(0, $user->getRoles());
    }
}
