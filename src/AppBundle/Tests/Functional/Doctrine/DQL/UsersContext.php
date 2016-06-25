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

namespace AppBundle\Tests\Functional\Doctrine\DQL;

use AppBundle\Model\User\User;
use AppBundle\Model\User\Util\DateTimeComparison;
use AppBundle\Tests\Functional\FixtureLoadingContext;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Feature context for user repository.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UsersContext extends FixtureLoadingContext implements SnippetAcceptingContext
{
    /**
     * @var bool
     */
    protected static $applyUserFixtures = false;

    /**
     * @var int
     */
    private $resultCount;

    /**
     * @var int[]
     */
    private $followerIds = [];

    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $filterResult;

    /** @BeforeScenario @user&&@repository */
    public function loadDataFixtures()
    {
        parent::loadDataFixtures();
    }

    /**
     * @When I try to delete all users with pending activation
     */
    public function iTryToDeleteAllUsersWithPendingActivation()
    {
        $repository        = $this->getEntityManager()->getRepository('Account:User');
        $this->resultCount = $repository->deletePendingActivationsByDate(new \DateTime('-2 hours'));
    }

    /**
     * @Then one user should still exist
     */
    public function oneUserShouldStillExist()
    {
        Assertion::count($this->getEntityManager()->getRepository('Account:User')->findAll(), 1);
    }

    /**
     * @Then two users should be removed
     */
    public function twoUsersShouldBeRemoved()
    {
        Assertion::eq(2, $this->resultCount);
    }

    /**
     * @When I ask for a list of follower ids for user :arg1
     */
    public function iAskForAListOfFollowerIdsForUser($arg1)
    {
        $this->followerIds = $this
            ->getEntityManager()
            ->getRepository('Account:User')
            ->getFollowingIdsByUser(
                $this->getEntityManager()->getRepository('Account:User')->findOneBy(['username' => $arg1])
            );
    }

    /**
     * @Then I should get two ids
     */
    public function iShouldGetTwoIds()
    {
        Assertion::count($this->followerIds, 2);
    }

    /**
     * @When I'd like to see a user by with username :arg1 and key :arg2
     */
    public function iDLikeToSeeAUserByWithUsernameAndKey($arg1, $arg2)
    {
        $this->user = $this->getEntityManager()->getRepository('Account:User')->findUserByUsernameAndActivationKey($arg1, $arg2);
    }

    /**
     * @Then I should see the user with id :arg1
     */
    public function iShouldSeeTheUserWithId($arg1)
    {
        Assertion::eq((int) $arg1, $this->user->getId());
    }

    /**
     * @Given the following auth data exist:
     */
    public function theFollowingAuthDataExist(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $user   = $row['affected'];
            $entity = $this->getEntityManager()->getRepository('Account:User')->findOneBy(['username' => $user]);
            $entity->addFailedAuthenticationWithIp($row['ip']);

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();

            $uid  = $entity->getId();
            $conn = $this->getEntityManager()->getConnection();

            $query = $conn
                ->prepare('SELECT `attemptId` FROM `FailedAuthAttempt2User` WHERE `userId` = :id');

            $query->execute([':id' => $uid]);
            $attemptId = $query->fetch()['attemptId'];

            $query = $conn->prepare('UPDATE `authentication_attempt` SET `latest_date_time` = :latest WHERE `id` = :id');
            $query->execute([':latest' => $row['latest'], ':id' => $attemptId]);
        }
    }

    /**
     * @When I delete ancient auth data
     */
    public function iDeleteAncientAuthData()
    {
        /** @var \AppBundle\Model\User\UserRepository $userRepository */
        $userRepository = $this->getRepository('Account:User');
        $userRepository->deleteAncientAttemptData(new \DateTime('-6 months'));
    }

    /**
     * @Then no log about :arg1 should exist on user :arg2 should exist
     */
    public function noLogAboutShouldExist($arg1, $arg2)
    {
        Assertion::false(
            $this->getRepository('Account:User')->findOneBy(['username' => $arg2])->exceedsIpFailedAuthAttemptMaximum($arg1, new DateTimeComparison())
        );
    }

    /**
     * @When I want to filter for non-unique usernames with the following data:
     */
    public function iWantToFilterForNonUniqueUsernamesWithTheFollowingData(TableNode $table)
    {
        $this->filterResult = $this->getRepository('Account:User')->filterUniqueUsernames(array_column($table->getHash(), 'username'));
    }

    /**
     * @Then I should see the following names:
     */
    public function iShouldSeeTheFollowingNames(TableNode $table)
    {
        $list = array_column($table->getHash(), 'username');

        Assertion::allInArray($this->filterResult, $list);
        Assertion::count($this->filterResult, count($list));
    }
}
