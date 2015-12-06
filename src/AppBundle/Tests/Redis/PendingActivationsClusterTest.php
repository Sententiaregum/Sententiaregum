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

namespace AppBundle\Tests\Redis;

use AppBundle\Model\User\PendingActivation;
use AppBundle\Model\User\User;
use AppBundle\Test\KernelTestCase;

class PendingActivationsClusterTest extends KernelTestCase
{
    protected function setUp()
    {
        static::bootKernel();
    }

    public function testCheckActivationKey()
    {
        $cluster = $this->getCluster();

        $key = $this->getActivationKey();
        $cluster->attachNewApproval($key);

        $this->assertTrue($cluster->checkApprovalByUser($this->createUserForKey($key)));
    }

    public function testExpiredActivationKey()
    {
        $cluster = $this->getCluster();

        $key = $this->getActivationKey();
        $cluster->attachNewApproval($key);

        $redis = self::$kernel->getContainer()->get('snc_redis.pending_activations');
        $redis->del('activation:'.$key); // simulate expiration

        $user       = $this->createUserForKey($key);
        $activation = new PendingActivation();
        $activation->setActivationDate(new \DateTime('-6 hours'));
        $user->setPendingActivation($activation);

        $this->assertFalse($cluster->checkApprovalByUser($user));
    }

    public function testExpiredKeyButValidDatabaseBackup()
    {
        $cluster = $this->getCluster();

        $key = $this->getActivationKey();
        $cluster->attachNewApproval($key);

        $redis = self::$kernel->getContainer()->get('snc_redis.pending_activations');
        $redis->del('activation:'.$key); // simulate expiration

        $this->assertTrue($cluster->checkApprovalByUser($this->createUserForKey($key)));
    }

    /**
     * Mock approval key.
     *
     * @return string
     */
    private function getActivationKey()
    {
        return md5(uniqid());
    }

    /**
     * @return \AppBundle\Redis\PendingActivationsCluster
     */
    private function getCluster()
    {
        return self::$kernel->getContainer()->get('app.redis.cluster.approval');
    }

    /**
     * @param string $key
     *
     * @return User
     */
    private function createUserForKey($key)
    {
        $user = new User();
        $user->setActivationKey($key);

        return $user;
    }
}
