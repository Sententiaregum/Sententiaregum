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

use AppBundle\Model\User\Provider\BlockedAccountReadInterface;
use AppBundle\Model\User\Provider\BlockedAccountWriteProviderInterface;
use Predis\Client;

/**
 * BlockedAccountCluster.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class BlockedAccountCluster implements BlockedAccountWriteProviderInterface, BlockedAccountReadInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function addTemporaryBlockedAccountID(string $user): void
    {
        $key = $this->buildStorageKeyWithID($user);

        $this->client->set($key, $key);
        $this->client->expire($key, 60);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountTemporaryBlocked(string $user): bool
    {
        return (bool) $this->client->exists($this->buildStorageKeyWithID($user));
    }

    /**
     * Builds the storage key with the user UUID for redis.
     *
     * @param string $id
     *
     * @return string
     */
    private function buildStorageKeyWithID(string $id): string
    {
        return sprintf('blocked:%s', $id);
    }
}
