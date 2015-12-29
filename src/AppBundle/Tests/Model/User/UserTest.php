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

        $user->setState(User::STATE_APPROVED);

        $user->addRole($role);
        $this->assertTrue($user->hasRole($role));

        $this->assertCount(1, $user->getRoles());
        $user->removeRole($role);
        $this->assertFalse($user->hasRole($role));

        $this->assertCount(0, $user->getRoles());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot attach role on non-approved user!
     */
    public function testRolesOnNonApprovedUser()
    {
        $user = new User();
        $role = new Role('ROLE_USER');

        $user->addRole($role);
    }

    public function testNewUserIp()
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de');

        $this->assertTrue($user->isNewUserIp($ip));
        $this->assertFalse($user->isNewUserIp($ip));
    }

    public function testFailedAuthIp()
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de');

        $user->addFailedAuthenticationWithIp($ip);
        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum($ip));

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum($ip));
    }

    public function testFailedAuthWithKnownIp()
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de');

        $this->assertTrue($user->isNewUserIp($ip));

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum($ip));
    }

    public function testSerialization()
    {
        $user = User::create('Ma27', 'foo', 'foo@bar.de');
        $user->setState(User::STATE_APPROVED);
        $user->addRole(new Role('ROLE_USER'));
        $user->isNewUserIp('33.33.33.33');

        $user->addFailedAuthenticationWithIp('127.0.0.1');
        $user->addFailedAuthenticationWithIp('127.0.0.1');
        $user->addFailedAuthenticationWithIp('127.0.0.1');

        $serialized = serialize($user);

        $newUser = unserialize($serialized);

        $this->assertSame('Ma27', $newUser->getUsername());
        $this->assertSame('foo', $newUser->getPassword());
        $this->assertSame('foo@bar.de', $newUser->getEmail());
        $this->assertInstanceOf(\DateTime::class, $newUser->getLastAction());
        $this->assertInstanceOf(\DateTime::class, $newUser->getRegistrationDate());
        $this->assertSame(User::STATE_APPROVED, $newUser->getState());
        $this->assertCount(1, $newUser->getRoles());
        $this->assertFalse($user->isNewUserIp('33.33.33.33'));
        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum('127.0.0.1'));
    }
}
