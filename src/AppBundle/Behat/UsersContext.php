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

use AppBundle\DataFixtures\ORM\AdminFixture;
use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\DataFixtures\ORM\UserFixture;
use AppBundle\Model\User\User;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Feature context for user repository.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UsersContext extends BaseContext implements SnippetAcceptingContext
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
     * @Given the user fixtures have been applied
     */
    public function theUserFixturesHaveBeenApplied()
    {
        $this
            ->getContainer()
            ->get('app.doctrine.fixtures_loader')
            ->applyFixtures([RoleFixture::class, AdminFixture::class, UserFixture::class]);
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
}
