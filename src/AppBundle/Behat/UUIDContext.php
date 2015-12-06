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
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

/**
 * BDD context for UUID service.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UUIDContext extends BaseContext implements SnippetAcceptingContext
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var User
     */
    private $user;

    /**
     * @Given there are no users
     */
    public function thereAreNoUsers()
    {
        (new ORMPurger($this->getEntityManager()))->purge();
    }

    /**
     * @When I generate a UUID for a user
     */
    public function iGenerateAUuidForAUser()
    {
        $this->user = new User();

        /** @var \AppBundle\Doctrine\ORM\UUID $service */
        $service    = $this->getContainer()->get('app.doctrine.uuid');
        $this->uuid = $service->generateUUIDForEntity($this->getEntityManager(), $this->user);
    }

    /**
     * @When I persist this user
     */
    public function iPersistThisUser()
    {
        $this->user->setId($this->uuid);
        $this->user->setUsername('Ma27_2');
        $this->user->setPassword('123456');
        $this->user->setEmail('Ma27_2@sen-ten-tia-re-gum.dev');

        $em = $this->getEntityManager();
        $em->persist($this->user);
        $em->flush();
    }

    /**
     * @Then I should have a valid uuid
     */
    public function iShouldHaveABitLongUuid()
    {
        Assertion::uuid($this->uuid);
    }

    /**
     * @Then I should be able to fetch the user
     */
    public function iShouldBeAbleToFetchTheUser()
    {
        $user = $this->getEntityManager()->find('Account:User', $this->uuid);

        Assertion::eq($this->user->getUsername(), $user->getUsername());
    }
}
