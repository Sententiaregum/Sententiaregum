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

use AppBundle\Test\KernelTestCase;

class OnlineUserIdClusterTest extends KernelTestCase
{
    public function testProvidedOnlineOfflineMap()
    {
        /** @var \AppBundle\Redis\OnlineUserIdCluster $service */
        $service = $this->getService('app.redis.cluster.online_users');

        $service->addUserId(1);
        $onlineOfflineMap = $service->validateUserIds([1, 2]);

        $this->assertArrayHasKey(1, $onlineOfflineMap);
        $this->assertArrayHasKey(2, $onlineOfflineMap);

        $this->assertTrue($onlineOfflineMap[1]);
        $this->assertFalse($onlineOfflineMap[2]);
    }
}
