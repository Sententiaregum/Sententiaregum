<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Redis;

use AppBundle\Model\User\Registration\Activation\ExpiredActivationProviderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Predis\Client;

/**
 * Redis aware implementation that checks whether the key is expired.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service("app.redis.cluster.approval")
 */
class PendingActivationsCluster implements ExpiredActivationProviderInterface
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * Constructor.
     *
     * @param Client $client
     *
     * @DI\InjectParams({
     *     "client" = @DI\Inject("snc_redis.pending_activations")
     * })
     */
    public function __construct(Client $client)
    {
        $this->redis = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function checkApprovalByActivationKey($activationKey)
    {
        $persistentKey = $this->generateRedisKeyByApprovalKey($activationKey);
        $exists        = $this->redis->exists($persistentKey);

        if ($exists) {
            $this->redis->del($persistentKey);
        }

        return $exists;
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
     * Generates a persistent key for redis.
     *
     * @param string $activationKey
     *
     * @return string
     */
    private function generateRedisKeyByApprovalKey($activationKey)
    {
        return sprintf('activation_%s', $activationKey);
    }
}
