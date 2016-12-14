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

namespace AppBundle\Tests\Acceptance\Integration\Redis;

use Assert\Assertion;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

/**
 * BlockedAccountClusterContext.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class BlockedAccountClusterContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var bool
     */
    private $isBlocked = false;

    /**
     * @var string
     */
    private $uuid;

    /** @AfterScenario */
    public function cleanUp(): void
    {
        $this->blockedUsers = false;
        $this->uuid         = null;
    }

    /**
     * @Given /^the account with UUID "(.*)" shows suspicious behavior$/
     *
     * @param string $uuid
     */
    public function theAccountWithUuidShowsSuspiciousBehavior(string $uuid): void
    {
        $this->uuid = $uuid;

        $this->getContainer()->get('app.redis.cluster.blocked_account')->addTemporaryBlockedAccountID($uuid);
    }

    /**
     * @When /^I check if "(.*)" is blocked$/
     *
     * @param string $uuid
     */
    public function loadBlockedUsers(string $uuid): void
    {
        $this->isBlocked = $this->getContainer()->get('app.redis.cluster.blocked_account')->isAccountTemporaryBlocked($uuid);
    }

    /**
     * @Then /^the block should be confirmed$/
     */
    public function ensureBlocked(): void
    {
        Assertion::true($this->isBlocked);
    }

    /**
     * @Given /^I wait more than one minute$/
     */
    public function simulateTime(): void
    {
        $this->getContainer()->get('snc_redis.blocked_users')->del([sprintf('blocked:%s', $this->uuid)]);
    }

    /**
     * @Then /^the block should not be confirmed$/
     */
    public function ensureNotBlocked(): void
    {
        Assertion::false($this->isBlocked);
    }
}
