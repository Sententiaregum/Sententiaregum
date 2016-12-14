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

namespace AppBundle\Tests\Acceptance\Integration\AggregateQuerying\User;

use AppBundle\DataFixtures\ORM\AdminFixture;
use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\DataFixtures\ORM\UserFixture;
use AppBundle\Model\Core\DTO\PaginatableDTO;
use AppBundle\Model\User\PendingActivation;
use AppBundle\Model\User\User;
use AppBundle\Model\User\Util\Date\DateTimeComparison;
use AppBundle\Tests\Acceptance\AbstractIntegrationContext;
use Assert\Assertion;
use Behat\Gherkin\Node\TableNode;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

/**
 * Feature context for user repository.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UsersContext extends AbstractIntegrationContext
{
    /**
     * @var int
     */
    private $resultCount = 0;

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

    /** @BeforeScenario */
    public function applyFixtures(): void
    {
        $this->getContainer()->get('app.doctrine.fixtures_loader')->applyFixtures([
            UserFixture::class,
            RoleFixture::class,
            AdminFixture::class,
        ]);
    }

    /** @AfterScenario */
    public function cleanUp(): void
    {
        $this->resultCount = 0;
        $this->followerIds = [];
        $this->user        = null;

        $this->getEntityManager()->clear();
    }

    /**
     * @Given /^a user with name "(.*)" has an expired activation$/
     *
     * @param string $username
     */
    public function createExpiredUser(string $username): void
    {
        $em = $this->getEntityManager();

        $user = User::create($username, '123456', sprintf('%s@sententiaregum.dev', $username), new PhpPasswordHasher());
        $r    = new \ReflectionProperty($user, 'pendingActivation');
        $r->setAccessible(true);
        $r->setValue($user, new PendingActivation(new \DateTime('-3 hours')));

        $em->persist($user);
        $em->flush();
    }

    /**
     * @When I try to delete all users with pending activation
     */
    public function deleteUsersWithExpiredPendingActivation(): void
    {
        $repository        = $this->getEntityManager()->getRepository('Account:User');
        $this->resultCount = $repository->deletePendingActivationsByDate(new \DateTime('-2 hours'));
    }

    /**
     * @Then /^the user "(.*)" should be removed$/
     *
     * @param string $username
     */
    public function ensureUserWasRemoved(string $username): void
    {
        Assertion::count($this->getEntityManager()->getRepository('Account:User')->findBy(['username' => $username]), 0);
    }

    /**
     * @Given /^the user "(.*)" is not activated and has activation key "(.*)"$/
     *
     * @param string $username
     * @param string $key
     */
    public function createNonActivatedUser(string $username, string $key): void
    {
        $em = $this->getEntityManager();

        $user = User::create($username, '123456', sprintf('%s@sententiaregum.dev', $username), new PhpPasswordHasher());
        $r    = new \ReflectionProperty($user, 'pendingActivation');
        $r->setAccessible(true);
        $r->setValue($user, new PendingActivation(new \DateTime(), $key));

        $em->persist($user);
        $em->flush();
    }

    /**
     * @Then /^I should get one result$/
     */
    public function ensureOneResult(): void
    {
        Assertion::isInstanceOf($this->user, User::class);
    }

    /**
     * @Then one user should still exist
     */
    public function oneUserShouldStillExist(): void
    {
        Assertion::count($this->getEntityManager()->getRepository('Account:User')->findAll(), 1);
    }

    /**
     * @Then two users should be removed
     */
    public function twoUsersShouldBeRemoved(): void
    {
        Assertion::eq(2, $this->resultCount);
    }

    /**
     * @When I ask for a list of follower ids for user :arg1 with limit :arg2 and offset :arg3
     */
    public function iAskForAListOfFollowerIdsForUser($arg1, $arg2, $arg3): void
    {
        $dto         = new PaginatableDTO();
        $dto->limit  = (int) $arg2;
        $dto->offset = (int) $arg3;

        $this->followerIds = $this
            ->getEntityManager()
            ->getRepository('Account:User')
            ->getFollowingIdsByUser(
                $this->getEntityManager()->getRepository('Account:User')->findOneBy(['username' => $arg1]),
                $dto
            );
    }

    /**
     * @Then I should get :arg1 ids
     */
    public function iShouldGetTwoIds($arg1): void
    {
        Assertion::count($this->followerIds, (int) $arg1);
    }

    /**
     * @When /^I'd like to see a user by with username "(.*)" and key "(.*)"$/
     *
     * @param string $username
     * @param string $key
     */
    public function findUserByNameAndKey(string $username, string $key): void
    {
        $this->user = $this->getEntityManager()->getRepository('Account:User')->findUserByUsernameAndActivationKey($username, $key);
    }

    /**
     * @Then I should see the user with id :arg1
     */
    public function iShouldSeeTheUserWithId($arg1): void
    {
        Assertion::eq((int) $arg1, $this->user->getId());
    }

    /**
     * @Given the following auth data exist:
     *
     * @param TableNode $table
     */
    public function ensureAuthAttemptData(TableNode $table): void
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
    public function removeAuthData(): void
    {
        /** @var \AppBundle\Service\Doctrine\Repository\UserRepository $userRepository */
        $userRepository = $this->getEntityManager()->getRepository('Account:User');
        $userRepository->deleteAncientAttemptData(new \DateTime('-6 months'));
    }

    /**
     * @Then /^no log about "(.*)" should exist on user "(.*)" should exist$/
     *
     * @param string $ip
     * @param string $username
     */
    public function ensureNoLogRemains(string $ip, string $username): void
    {
        Assertion::false(
            $this->getEntityManager()->getRepository('Account:User')
                ->findOneBy(['username' => $username])->exceedsIpFailedAuthAttemptMaximum($ip, new DateTimeComparison())
        );
    }

    /**
     * @When I want to filter for non-unique usernames with the following data:
     */
    public function filterForUniqueUsernames(TableNode $table): void
    {
        $this->filterResult = $this->getEntityManager()->getRepository('Account:User')->filterUniqueUsernames(array_column($table->getHash(), 'username'));
    }

    /**
     * @Then I should see the following names:
     *
     * @param TableNode $table
     */
    public function validateNames(TableNode $table): void
    {
        $list = array_column($table->getHash(), 'username');

        Assertion::allInArray($this->filterResult, $list);
        Assertion::count($this->filterResult, count($list));
    }

    /**
     * @When I try to persist the following user:
     *
     * @param TableNode $table
     */
    public function persistUser(TableNode $table): void
    {
        $row  = $table->getRow(1);
        $user = User::create($row[0], $row[1], $row[2], new PhpPasswordHasher());

        $this->getEntityManager()->getRepository('Account:User')->save($user);
        $this->user = $user;
    }

    /**
     * @Then it should be present in the identity map
     */
    public function ensureInIdentityMap(): void
    {
        Assertion::true($this->getEntityManager()->getUnitOfWork()->isInIdentityMap($this->user));
    }

    /**
     * @Then it should be scheduled for insert
     */
    public function ensureScheduledForPersist(): void
    {
        Assertion::true($this->getEntityManager()->getUnitOfWork()->isScheduledForInsert($this->user));
    }

    /**
     * @When I try to remove the user :arg1
     *
     * @param string $username
     */
    public function removeUser(string $username): void
    {
        $repository = $this->getEntityManager()->getRepository('Account:User');
        $repository->remove($this->user = $repository->findOneBy(['username' => $username]));
    }

    /**
     * @Then it should be scheduled for removal
     */
    public function ensureScheduledForRemoval(): void
    {
        Assertion::true($this->getEntityManager()->getUnitOfWork()->isScheduledForDelete($this->user));
    }
}
