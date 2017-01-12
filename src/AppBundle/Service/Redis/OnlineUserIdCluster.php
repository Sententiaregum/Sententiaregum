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

namespace AppBundle\Service\Redis;

use AppBundle\Model\User\Provider\OnlineUserIdReadProviderInterface;
use AppBundle\Model\User\Provider\OnlineUserIdWriteProviderInterface;
use Predis\Client as Redis;

/**
 * Concrete redis aware implementation of a data provider for user ids of online users.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
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
    public function addUserId(string $userId): void
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
        return array_combine($ids, array_map([$this, 'exists'], $ids));
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

    /**
     * Checks if the given user id is existant in the online users cluster of the redis database.
     *
     * @param string $userId
     *
     * @return bool
     */
    private function exists(string $userId): bool
    {
        return (bool) $this->redis->exists($this->createRedisStorageKeyByUserId($userId));
    }
}
