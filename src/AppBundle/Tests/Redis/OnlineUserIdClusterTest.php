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
        $service     = $this->getService('app.redis.cluster.online_users');
        /** @var \AppBundle\Doctrine\ORM\UUID $uuidService */
        $uuidService = $this->getService('app.doctrine.uuid');
        $ids         = [];
        for ($i = 0; $i < 2; $i++) {
            $ids[] = $uuidService->generateUUIDForEntity(
                $this->getService('doctrine.orm.default_entity_manager'),
                new \stdClass()
            );
        }

        $service->addUserId($ids[0]);
        $onlineOfflineMap = $service->validateUserIds($ids);

        $this->assertArrayHasKey($ids[0], $onlineOfflineMap);
        $this->assertArrayHasKey($ids[1], $onlineOfflineMap);

        $this->assertTrue($onlineOfflineMap[$ids[0]]);
        $this->assertFalse($onlineOfflineMap[$ids[1]]);
    }
}
