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

namespace AppBundle\Service\Redis;

use AppBundle\Model\User\Provider\OnlineUserIdReadProviderInterface;
use AppBundle\Model\User\Provider\OnlineUserIdWriteProviderInterface;
use Predis\Client as Redis;

/**
 * Concrete redis aware implementation of a data provider for user ids of online users.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OnlineUserIdCluster implements OnlineUserIdReadProviderInterface, OnlineUserIdWriteProviderInterface
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * Constructor.
     *
     * @param Redis $redis
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function addUserId(string $userId)
    {
        $key = $this->createRedisStorageKeyByUserId($userId);

        $this->redis->set($key, $key);
        $this->redis->expire($key, 60 * 2);
    }

    /**
     * {@inheritdoc}
     */
    public function validateUserIds(array $ids): array
    {
        $that             = &$this;
        $onlineOfflineMap = array_map(
            function ($userId) use ($that) {
                return $that->redis->exists($that->createRedisStorageKeyByUserId($userId));
            },
            $ids
        );

        return array_combine($ids, $onlineOfflineMap);
    }

    /**
     * Creates the id for persistent redis keys.
     *
     * @param string $id
     *
     * @return string
     */
    private function createRedisStorageKeyByUserId(string $id): string
    {
        return sprintf('online:%s', $id);
    }
}
