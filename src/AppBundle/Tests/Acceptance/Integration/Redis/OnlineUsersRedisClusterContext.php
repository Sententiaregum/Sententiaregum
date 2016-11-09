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
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

/**
 * Behat context for the basic behavior of the cluster containing the
 * data of online users.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OnlineUsersRedisClusterContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var string[]
     */
    private $result = [];

    /** @AfterScenario */
    public function cleanUp()
    {
        $this->result = [];
    }

    /**
     * @Given /^the following users are online:$/
     *
     * @param TableNode $node
     */
    public function ensureUsersAreOnline(TableNode $node)
    {
        $service = $this->getContainer()->get('app.redis.cluster.online_users');

        foreach ($node->getHash() as $data) {
            $service->addUserId($data['uuid']);
        }
    }

    /**
     * @When /^I check the following UUIDs:$/
     *
     * @param TableNode $table
     */
    public function checkIdList(TableNode $table)
    {
        $userIdList = array_map(
            function ($row) {
                return $row['uuid'];
            },
            $table->getHash()
        );

        $this->result = $this->getContainer()->get('app.redis.cluster.online_users')->validateUserIds($userIdList);
    }

    /**
     * @Then I should see the following result:
     */
    public function iShouldSeeTheFollowingResult(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $userId = $row['uuid'];
            $state  = $row['is_active'] === 'true';

            Assertion::keyExists($this->result, $userId);
            Assertion::eq($state, $this->result[$userId]);
        }

        Assertion::eq(count(iterator_to_array($table)), count($this->result));
    }
}
