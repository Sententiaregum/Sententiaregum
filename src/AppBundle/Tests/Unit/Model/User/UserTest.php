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

namespace AppBundle\Tests\Model\User;

use AppBundle\Model\User\Role;
use AppBundle\Model\User\User;
use AppBundle\Model\User\Util\Date\DateTimeComparison;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testLockUnlock(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $this->assertFalse($user->isLocked());

        $user->performStateTransition(User::STATE_APPROVED); // state transition (new -> approved) as non-approved users can't be locked
        $user->performStateTransition(User::STATE_LOCKED);
        $this->assertTrue($user->isLocked());

        $user->performStateTransition(User::STATE_APPROVED);
        $this->assertFalse($user->isLocked());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Approved users cannot have an activation key!
     */
    public function testSetActivationKeyOnApprovedUser(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->performStateTransition(User::STATE_APPROVED);

        $user->storeUniqueActivationKeyForNonApprovedUser('any long activation key');
    }

    public function testRemoveActivationKey(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->storeUniqueActivationKeyForNonApprovedUser('any long api key');
        $user->performStateTransition(User::STATE_APPROVED, 'any long api key');

        $this->assertSame(User::STATE_APPROVED, $user->getState());
    }

    public function testFactory(): void
    {
        $hasher = new PhpPasswordHasher();
        $user   = User::create('Ma27', 'test', 'Ma27@sententiaregum.dev', $hasher);

        $this->assertSame('Ma27', $user->getUsername());
        $this->assertTrue($hasher->compareWith($user->getPassword(), 'test'));
        $this->assertSame('Ma27@sententiaregum.dev', $user->getEmail());
    }

    public function testRemoveApiKey(): void
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

    public function testFollower(): void
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

    public function testRoles(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $role = new Role('ROLE_USER');

        $user->performStateTransition(User::STATE_APPROVED);

        $user->addRole($role);
        $this->assertTrue($user->hasRole($role));

        $this->assertCount(1, $user->getRoles());
        $user->removeRole($role);
        $this->assertFalse($user->hasRole($role));

        $this->assertCount(0, $user->getRoles());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot attach role on non-approved or locked user!
     */
    public function testRolesOnNonApprovedUser(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $role = new Role('ROLE_USER');

        $user->addRole($role);
    }

    public function testNewUserIp(): void
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $this->assertTrue($user->addAndValidateNewUserIp($ip, new DateTimeComparison()));
        $this->assertFalse($user->addAndValidateNewUserIp($ip, new DateTimeComparison()));
    }

    public function testFailedAuthIp(): void
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $user->addFailedAuthenticationWithIp($ip);
        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));
    }

    public function testFailedAuthWithKnownIp(): void
    {
        $ip   = '127.0.0.1';
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $this->assertTrue($user->addAndValidateNewUserIp($ip, new DateTimeComparison()));

        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);
        $user->addFailedAuthenticationWithIp($ip);

        $this->assertFalse($user->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison()));
    }

    public function testFailedAuthInRange(): void
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

    public function testNewUserIpWithFailedAuthenticationsLeadToCorruptionWarning(): void
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

    public function testNewUserIpCauseRemovalOfOlderLoginIssues(): void
    {
        $mock = $this->createMock(DateTimeComparison::class);
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

    public function testFailedAuthOutOfRange(): void
    {
        $mock = $this->createMock(DateTimeComparison::class);
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

    public function testAuthCheckForNonRegisteredIP(): void
    {
        $user = User::create('Ma27', '123456', 'foo@bar.de', new PhpPasswordHasher());

        $this->assertFalse(
            $user->exceedsIpFailedAuthAttemptMaximum('127.0.0.1', $this->createMock(DateTimeComparison::class))
        );
    }

    public function testSerialization(): void
    {
        $hasher = new PhpPasswordHasher();
        $user   = User::create('Ma27', 'foo', 'foo@bar.de', $hasher);

        $user->performStateTransition(User::STATE_APPROVED);
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
        $this->assertSame(User::STATE_APPROVED, $newUser->getState());
        $this->assertCount(1, $newUser->getRoles());
        $this->assertFalse($user->addAndValidateNewUserIp('33.33.33.33', new DateTimeComparison()));
        $this->assertTrue($user->exceedsIpFailedAuthAttemptMaximum('127.0.0.1', new DateTimeComparison()));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid activation key given!
     */
    public function testStateChangeWithoutActivationKey(): void
    {
        $user = User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->storeUniqueActivationKeyForNonApprovedUser(uniqid());
        $user->performStateTransition(User::STATE_APPROVED);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid state!
     */
    public function testInvalidState(): void
    {
        $user = User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->performStateTransition('any random state');
    }

    public function testActivationLifecycle(): void
    {
        $user = User::create('Ma27', 'foo', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $this->assertSame(User::STATE_NEW, $user->getState());

        $activationKey = 'a long activation key'; // to be generated by a domain service
        $user->storeUniqueActivationKeyForNonApprovedUser($activationKey);
        $this->assertSame($activationKey, $user->getPendingActivation()->getKey());

        $user->performStateTransition(User::STATE_APPROVED, $activationKey);
        $this->assertSame(User::STATE_APPROVED, $user->getState());
        $this->assertNull($user->getPendingActivation());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Old password is invalid, but must be given to change it!
     */
    public function testUpdatePasswordWithInvalidOldOne(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->setOrUpdatePassword('123456', new PhpPasswordHasher());
        $user->setOrUpdatePassword('1234567', new PhpPasswordHasher(), 'invalid old one');
    }

    public function testUpdatePassword(): void
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
    public function testInvalidLocale(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->modifyUserLocale('DE');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Role "ROLE_USER" already attached at user "Ma27"!
     */
    public function testAddRoleTwice(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $role = new Role('ROLE_USER');

        $user->performStateTransition(User::STATE_APPROVED);
        $user->addRole($role);
        $user->addRole($role);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot remove not existing role "ROLE_USER"!
     */
    public function testRemoveNonExistentRole(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $role = new Role('ROLE_USER');

        $user->removeRole($role);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot remove relation with invalid user "benbieler"!
     */
    public function testTryToRemoveInvalidFollowing(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->removeFollowing(User::create('benbieler', '123456', 'benbieler@sententiaregum.dev', new PhpPasswordHasher()));
    }

    public function testModifyLocale(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $this->assertSame('en', $user->getLocale());
        $user->modifyUserLocale('de');

        $this->assertSame('de', $user->getLocale());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Only approved users can remove activation keys!
     */
    public function testTryRemoveActivationKeyAfterStateTransition(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->performStateTransition(User::STATE_APPROVED);

        $user->removeActivationKey();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Only approved users can be locked!
     */
    public function testTransitionFromNewToLocked(): void
    {
        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev', new PhpPasswordHasher());
        $user->performStateTransition(User::STATE_LOCKED);
    }
}
