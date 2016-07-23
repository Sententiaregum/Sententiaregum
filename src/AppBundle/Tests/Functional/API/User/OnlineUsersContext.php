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

namespace AppBundle\Tests\Functional\API\User;

use AppBundle\Tests\Functional\FixtureLoadingContext;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Behat context for the online users behavior.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OnlineUsersContext extends FixtureLoadingContext implements SnippetAcceptingContext
{
    /**
     * @var bool[]
     */
    private $response;

    /** @BeforeScenario @user&&@online_users */
    public function loadDataFixtures()
    {
        parent::loadDataFixtures();
    }

    /**
     * @Given this user follows the following users:
     *
     * @param TableNode $table
     */
    public function thisUserFollowsTheFollowingUsers(TableNode $table)
    {
        $entityManager = $this->getEntityManager();
        $repo          = $entityManager->getRepository('Account:User');
        $currentUser   = $repo->findOneBy(['username' => 'test_1']);

        foreach ($table->getHash() as $row) {
            $user = $repo->findOneBy(['username' => $row['username']]);

            $currentUser->addFollowing($user);
        }

        $entityManager->persist($currentUser);
        $entityManager->flush();
    }

    /**
     * @Given the following users are online:
     *
     * @param TableNode $table
     */
    public function theFollowingUsersAreOnline(TableNode $table)
    {
        /** @var \AppBundle\Model\User\Provider\OnlineUserIdReadProviderInterface $cluster */
        $cluster = $this->getContainer()->get('app.redis.cluster.online_users');
        $repo    = $this->getEntityManager()->getRepository('Account:User');

        foreach ($table->getHash() as $row) {
            $username = $row['username'];
            $id       = $repo->findOneBy(['username' => $username])->getId();

            $cluster->addUserId($id);
        }
    }

    /**
     * @When I ask for a list containing online users
     */
    public function theUserAsksForAListContainingOnlineUsers()
    {
        $this->response = $this->performRequest(
            'GET',
            '/api/protected/users/online.json',
            [],
            true,
            [],
            [],
            200,
            true
        );
    }

    /**
     * @Then I should see the following data:
     *
     * @param TableNode $table
     */
    public function heShouldSeeTheFollowingData(TableNode $table)
    {
        $hash = $table->getHash();
        $em   = $this->getEntityManager();
        Assertion::count($this->response, count($hash));

        foreach ($hash as $row) {
            $username = $row['username'];
            $isOnline = $row['is_online'] === 'true';

            $userId = $em->getRepository('Account:User')->findOneBy(['username' => $username])->getId();
            Assertion::keyExists($this->response, $userId);
            Assertion::same($isOnline, (bool) $this->response[$userId]);
        }
    }
}
