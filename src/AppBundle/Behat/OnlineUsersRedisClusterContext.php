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

namespace AppBundle\Behat;

use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Behat context for the basic behavior of the cluster containing the
 * data of online users.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OnlineUsersRedisClusterContext extends FixtureLoadingContext implements SnippetAcceptingContext
{
    /**
     * @var bool
     */
    protected static $applyUserFixtures = false;

    /**
     * @var string
     */
    private $result;

    /**
     * @Given the user with id :arg1 will be marked as online
     */
    public function theUserWithIdWillBeMarkedAsOnline($arg1)
    {
        $this->getContainer()->get('app.redis.cluster.online_users')->addUserId((int) $arg1);
    }

    /**
     * @When I'd like to know the state of the following user ids:
     */
    public function iDLikeToKnowTheStateOfTheFollowingUserIds(TableNode $table)
    {
        $userIdList = array_map(
            function ($row) {
                return (int) $row['user_id'];
            },
            $table->getHash()
        );

        $this->result = array_combine(
            $userIdList,
            $this->getContainer()->get('app.redis.cluster.online_users')->validateUserIds($userIdList)
        );
    }

    /**
     * @Then I should see the following result:
     */
    public function iShouldSeeTheFollowingResult(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $userId = (int) $row['user_id'];
            $state  = $row['state'] === 'true';

            Assertion::keyExists($this->result, $userId);
            Assertion::eq($state, $this->result[$userId]);
        }

        Assertion::eq(count(iterator_to_array($table)), count($this->result));
    }
}
