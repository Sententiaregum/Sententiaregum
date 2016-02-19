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

namespace AppBundle\Tests\Functional\API\User;

use AppBundle\Tests\Functional\AppContext;
use AppBundle\Tests\Functional\BaseTrait;
use AppBundle\Tests\Functional\FixtureLoadingContext;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Context for user profile behavior.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UserDetailsContext extends FixtureLoadingContext implements SnippetAcceptingContext
{
    use BaseTrait;

    /**
     * @var array
     */
    private $response;

    /**
     * @BeforeScenario @user&&@details
     */
    public function loadDataFixtures()
    {
        parent::loadDataFixtures();
    }

    /**
     * @When I attempt to gather sensitive data
     */
    public function iAttemptToGatherSensitiveData()
    {
        $this->response = $this->performRequest('GET', '/api/protected/users/credentials.json');
    }

    /**
     * @Then I should see the following information:
     */
    public function iShouldSeeTheFollowingInformation(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $name  = $row['username'];
            $roles = $row['roles'];

            Assertion::eq($name, $this->response['username']);
            Assertion::eq($roles, implode(', ', array_column($this->response['roles'], 'role')));
        }
    }

    /**
     * @Then the proper api key should be adjusted
     */
    public function theProperApiKeyShouldBeAdjusted()
    {
        Assertion::eq($this->response['api_key'], AppContext::$apiKey);
    }
}
