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

namespace AppBundle\Redis;

use AppBundle\Model\User\Online\OnlineUserIdDataProviderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Predis\Client as Redis;

/**
 * Concrete redis aware implementation of a data provider for user ids of online users.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service("app.redis.cluster.online_users")
 */
class OnlineUserIdCluster implements OnlineUserIdDataProviderInterface
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * Constructor.
     *
     * @param Redis $redis
     *
     * @DI\InjectParams({
     *     "redis" = @DI\Inject("snc_redis.online_users")
     * })
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function addUserId($userId)
    {
        $key = $this->createRedisStorageKeyByUserId($userId);

        $this->redis->set($key, $key);
        $this->redis->expire($key, 60 * 2);
    }

    /**
     * {@inheritdoc}
     */
    public function validateUserIds(array $ids)
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
     * @param int $id
     *
     * @return string
     */
    private function createRedisStorageKeyByUserId($id)
    {
        return sprintf('online:%s', $id);
    }
}
