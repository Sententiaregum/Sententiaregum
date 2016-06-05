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

use AppBundle\Model\User\Registration\Activation\ExpiredActivationProviderInterface;
use AppBundle\Model\User\User;
use Predis\Client as Redis;

/**
 * Redis aware implementation that checks whether the key is expired.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class PendingActivationsCluster implements ExpiredActivationProviderInterface
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * Constructor.
     *
     * @param Redis $client
     */
    public function __construct(Redis $client)
    {
        $this->redis = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function checkApprovalByUser(User $user)
    {
        if (!$this->lookupKeyInRedisDatabase($user->getPendingActivation()->getKey())) {
            // the associated model will be loaded through lazy loading (doctrine sends a call to the db using a proxy for the model).
            // this is because the model representing parts of the approval process must be hydrated through
            // doctrine's complex hydration algorithm. In case of issues with redis, this data can be used
            // as backup, but the hydration of one-to-one relations is slow, so redis will be used by default.
            return !$user->getPendingActivation()->isActivationExpired();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attachNewApproval($activationKey)
    {
        $key = $this->generateRedisKeyByApprovalKey($activationKey);

        $this->redis->set($key, $key);
        $this->redis->expire($key, 3600 * 2);
    }

    /**
     * Checks if the activation key is present in the redis database.
     *
     * @param string $activationKey
     *
     * @return bool
     */
    private function lookupKeyInRedisDatabase($activationKey)
    {
        $persistentKey = $this->generateRedisKeyByApprovalKey($activationKey);
        $exists        = $this->redis->exists($persistentKey);

        // the key is not needed anymore because the approval validation will be triggered once only.
        if ($exists) {
            $this->redis->del($persistentKey);
        }

        return $exists;
    }

    /**
     * Generates a persistent key for redis.
     *
     * @param string $activationKey
     *
     * @return string
     */
    private function generateRedisKeyByApprovalKey($activationKey)
    {
        return sprintf('activation:%s', $activationKey);
    }
}
