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

namespace AppBundle\Tests\Acceptance\Functional\User;

use AppBundle\Tests\Acceptance\ContextHelper;
use Assert\Assertion;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

/**
 * OnlineUsersContext.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OnlineUsersContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var \AppBundle\Tests\Acceptance\ApiContext
     */
    private $apiContext;

    /** @BeforeScenario */
    public function connectToAPiContext(BeforeScenarioScope $scope)
    {
        return ContextHelper::connectToAPIContext($scope);
    }

    /** @AfterScenario */
    public function dropApiContext()
    {
        $this->apiContext = null;
    }

    /**
     * @Given /^the following users are online:$/
     *
     * @param TableNode $node
     */
    public function ensureUsersAreOnline(TableNode $node)
    {
        /** @var \Symfony\Bundle\FrameworkBundle\Client $client */
        $client = $this->getContainer()->get('test.client');

        foreach ($node->getHash() as $data) {
            $client->request('POST', '/api/api-key.json', ['login' => $data['username'], 'password' => $data['password']]);

            // send another request to declare the user as online
            $client->request('GET', '/api/protected/users/credentials.json', [], [], [
                'HTTP_X-API-KEY' => json_decode($client->getResponse()->getContent(), true)['apiKey']
            ]);
        }
    }

    /**
     * @Then /^the following users should be active:$/
     *
     * @param TableNode $node
     */
    public function checkUserActivity(TableNode $node)
    {
        /** @var \AppBundle\Service\Doctrine\Repository\UserRepository $repository */
        $repository = $this->getContainer()->get('app.repository.user');

        Assertion::eq(
            $this->apiContext->getResponse(),
            array_combine(
                array_map(function (array $data) use ($repository):string {
                    return $repository->findOneBy(['username' => $data['username']])->getId();
                }, $node->getHash()),
                array_fill(0, 1, 1)
            )
        );
    }
}
