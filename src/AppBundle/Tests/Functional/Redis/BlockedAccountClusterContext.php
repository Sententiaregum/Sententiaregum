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

namespace AppBundle\Tests\Functional\Redis;

use AppBundle\Tests\Functional\FixtureLoadingContext;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * BlockedAccountClusterContext.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class BlockedAccountClusterContext extends FixtureLoadingContext implements SnippetAcceptingContext
{
    /** @BeforeScenario @user&&@blocked_user_cluster */
    public function loadDataFixtures()
    {
        parent::loadDataFixtures();
    }

    /**
     * @When the account with UUID :arg1 shows suspicious behavior
     */
    public function theAccountWithUuidShowsSuspiciousBehavior($arg1)
    {
        $this->getContainer()->get('app.redis.cluster.blocked_account')->addTemporaryBlockedAccountID($arg1);
    }

    /**
     * @Then the account with UUID :arg1 should be present in the cluster
     */
    public function theAccountWithUuidShouldBePresentInTheCluster($arg1)
    {
        Assertion::true($this->getContainer()->get('app.redis.cluster.blocked_account')->isAccountTemporaryBlocked($arg1));
    }
}
