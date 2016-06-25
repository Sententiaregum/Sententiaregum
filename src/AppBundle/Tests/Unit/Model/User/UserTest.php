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

namespace AppBundle\Tests\Model\User;

use AppBundle\Model\User\Role;
use AppBundle\Model\User\User;
use AppBundle\Model\User\Util\DateTimeComparison;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testLockUnlock()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
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
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->modifyActivationStatus(User::STATE_APPROVED);

        $user->storeUniqueActivationKeyForNonApprovedUser('any long activation key');
    }

    public function testRemoveActivationKey()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->storeUniqueActivationKeyForNonApprovedUser('any long api key');
        $user->modifyActivationStatus(User::STATE_APPROVED, 'any long api key');

        $this->assertSame(User::STATE_APPROVED, $user->getActivationStatus());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot set empty activation key! Please call "removeActivationKey()" instead for the removal of the activation key!
     */
    public function testEmptyActivationKey()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->storeUniqueActivationKeyForNonApprovedUser(null);
    }

    public function testFactory()
    {
        $hasher = new PhpPasswordHasher();
        $user   = User::create('Ma27', 'test', 'Ma27@sententiaregum.dev', $hasher);

        $this->assertSame('Ma27', $user->getUsername());
        $this->assertTrue($hasher->compareWith($user->getPassword(), 'test'));
        $this->assertSame('Ma27@sententiaregum.dev', $user->getEmail());
    }

    public function testRemoveApiKey()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());

        $p = new \ReflectionProperty($user, 'apiKey');
        $p->setAccessible(true);
        $this->assertEmpty($user->getApiKey());
        $p->setValue($user, 'key');
        $this->assertNotEmpty($user->getApiKey());
        $p->setValue($user, null);
        $this->assertEmpty($user->getApiKey());
    }

    public function testFollower()
    {
        $user      = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $following = User::create('benbieler', '123456', 'bebieler@sententiaregum.dev', new PhpPasswordHasher());

        $user->addFollowing($following);
        $this->assertTrue($user->follows($following));

        $this->assertCount(1, $user->getFollowing());

        $user->removeFollowing($following);
        $this->assertFalse($user->follows($following));
        $this->assertCount(0, $user->getFollowing());
    }

    public function testRoles()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $role = new Role('ROLE_USER');

        $user->modifyActivationStatus(User::STATE_APPROVED);

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
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $role = new Role('ROLE_USER');

        $user->addRole($role);
    }

    public function testNewUserIp()
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $this->assertTrue($user->addAndValidateNewUserIp($ip, new DateTimeComparison()));
        $this->assertFalse($user->addAndValidateNewUserIp($ip, new DateTimeComparison()));
    }

    public function testFailedAuthIp()
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $user->addFailedAuthenticationWithIp($ip);
        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));
    }

    public function testFailedAuthWithKnownIp()
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $this->assertTrue($user->addAndValidateNewUserIp($ip, new DateTimeComparison()));

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));
    }

    public function testFailedAuthInRange()
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));
    }

    public function testNewUserIpWithFailedAuthenticationsLeadToCorruptionWarning()
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addAndValidateNewUserIp($ip, new DateTimeComparison());

        // nothing will be deleted
        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));
    }

    public function testNewUserIpCauseRemovalOfOlderLoginIssues()
    {
        $mock = $this->getMock(DateTimeComparison::class);
        $mock
            ->expects($this->any())
            ->method('__invoke')
            ->willReturn(false);

        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addAndValidateNewUserIp($ip, $mock);

        // nothing will be deleted
        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));
    }

    public function testFailedAuthOutOfRange()
    {
        $mock = $this->getMock(DateTimeComparison::class);
        $mock
            ->expects($this->any())
            ->method('__invoke')
            ->willReturn(false);

        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum($ip, $mock));

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum($ip, $mock));
    }

    public function testAuthCheckForNonRegisteredIP()
    {
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $this->assertFalse(
            $user->exceedsIpFailedAuthAttemptMaximum('127.0.0.1', $this->getMock(DateTimeComparison::class))
        );
    }

    public function testSerialization()
    {
        $hasher = new PhpPasswordHasher();
        $user   = User::create('Ma27', 'foo', 'foo@bar.de', $hasher);

        $user->modifyActivationStatus(User::STATE_APPROVED);
        $user->addRole(new Role('ROLE_USER'));
        $user->addAndValidateNewUserIp('33.33.33.33', new DateTimeComparison());

        $user->addFailedAuthenticationWithIp('127.0.0.1');
        $user->addFailedAuthenticationWithIp('127.0.0.1');
        $user->addFailedAuthenticationWithIp('127.0.0.1');

        $serialized = serialize($user);

        $newUser = unserialize($serialized);

        $this->assertSame('Ma27', $newUser->getUsername());
        $this->assertTrue($hasher->compareWith($user->getPassword(), 'foo'));
        $this->assertSame('foo@bar.de', $newUser->getEmail());
        $this->assertInstanceOf(\DateTime::class, $newUser->getLastAction());
        $this->assertInstanceOf(\DateTime::class, $newUser->getRegistrationDate());
        $this->assertSame(User::STATE_APPROVED, $newUser->getActivationStatus());
        $this->assertCount(1, $newUser->getRoles());
        $this->assertFalse($user->addAndValidateNewUserIp('33.33.33.33', new DateTimeComparison()));
        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum('127.0.0.1', new DateTimeComparison()));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid activation key given!
     */
    public function testStateChangeWithoutActivationKey()
    {
        $user = User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->storeUniqueActivationKeyForNonApprovedUser(uniqid());
        $user->modifyActivationStatus(User::STATE_APPROVED);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid state!
     */
    public function testInvalidState()
    {
        $user = User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->modifyActivationStatus('any random state');
    }

    public function testActivationLifecycle()
    {
        $user = User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $this->assertSame(User::STATE_NEW, $user->getActivationStatus());

        $activationKey = 'a long activation key'; // to be generated by a domain service
        $user->storeUniqueActivationKeyForNonApprovedUser($activationKey);
        $this->assertSame($activationKey, $user->getPendingActivation()->getKey());

        $user->modifyActivationStatus(User::STATE_APPROVED, $activationKey);
        $this->assertSame(User::STATE_APPROVED, $user->getActivationStatus());
        $this->assertNull($user->getPendingActivation());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Old password is invalid, but must be given to change it!
     */
    public function testUpdatePasswordWithInvalidOldOne()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->setOrUpdatePassword('123456', new PhpPasswordHasher());
        $user->setOrUpdatePassword('1234567', new PhpPasswordHasher(), 'invalid old one');
    }

    public function testUpdatePassword()
    {
        $hasher = new PhpPasswordHasher();
        $user   = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());

        $user->setOrUpdatePassword('1234567', $hasher, '123456');

        $this->assertTrue($hasher->compareWith($user->getPassword(), '1234567'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid locale!
     */
    public function testInvalidLocale()
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->modifyUserLocale('DE');
    }
}
