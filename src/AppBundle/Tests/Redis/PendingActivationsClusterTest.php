<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Redis;

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

        $this->assertTrue($cluster->checkApprovalByActivationKey($key));
        $this->assertFalse($cluster->checkApprovalByActivationKey($key)); // activations should be checked once, after that they must be removed
    }

    public function testExpiredActivationKey()
    {
        $cluster = $this->getCluster();

        $key = $this->getActivationKey();
        $cluster->attachNewApproval($key);

        $redis = self::$kernel->getContainer()->get('snc_redis.pending_activations');
        $redis->del('activation_'.$key); // simulate expiration

        $this->assertFalse($cluster->checkApprovalByActivationKey($key));
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
}
