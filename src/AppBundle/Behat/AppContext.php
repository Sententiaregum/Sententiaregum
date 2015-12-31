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

use AppBundle\Model\User\User;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

/**
 * Behat context class containing basic steps.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class AppContext extends BaseContext implements SnippetAcceptingContext
{
    /**
     * @var string
     */
    public static $apiKey;

    /**
     * @Given the database is purged
     */
    public function theDatabaseIsPurged()
    {
        (new ORMPurger($this->getEntityManager()))->purge();
    }

    /**
     * @Given the following users exist:
     */
    public function theFollowingUsersExist(TableNode $table)
    {
        /** @var \Ma27\ApiKeyAuthenticationBundle\Model\Password\PasswordHasherInterface $hasher */
        $hasher = $this->getContainer()->get('ma27_api_key_authentication.password.strategy');
        $em     = $this->getEntityManager();

        $userRole  = $em->getRepository('Account:Role')->findOneBy(['role' => 'ROLE_USER']);
        $adminRole = $em->getRepository('Account:Role')->findOneBy(['role' => 'ROLE_ADMIN']);

        foreach ($table->getHash() as $row) {
            $user = User::create($row['username'], $hasher->generateHash($row['password']), $row['email']);

            if (isset($row['user_id'])) {
                // there are cases where the user id should be known
                $r = new \ReflectionProperty(User::class, 'id');
                $r->setAccessible(true);
                $r->setValue($user, $row['user_id']);
            }

            if (isset($row['activation_date'])) {
                $user->getPendingActivation()->setActivationDate(new \DateTime($row['activation_date']));
            }

            if (!(isset($row['is_non_activated']) && $row['is_non_activated'] === 'true')) {
                $user->setState(User::STATE_APPROVED);

                // roles only allowed for approved users
                $user->addRole($userRole);
                if (isset($row['is_admin']) && $row['is_admin'] === 'true') {
                    $user->addRole($adminRole);
                }
            } else {
                if (isset($row['activation_key'])) {
                    $user->setActivationKey($row['activation_key']);
                }
            }

            $em->persist($user);
        }

        $em->flush();
    }

    /**
     * @Given I'm logged in as :arg1 with password :arg2
     */
    public function iAmLoggedInAs($arg1, $arg2)
    {
        static::$apiKey = $this->authenticate($arg1, $arg2);
    }
}
