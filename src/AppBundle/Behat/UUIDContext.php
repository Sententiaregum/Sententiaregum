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

use AppBundle\Behat\Doctrine\EmptyEntityManager;
use AppBundle\Doctrine\ORM\UUID;
use AppBundle\Model\User\User;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * BDD context for UUID service.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UUIDContext extends BaseContext implements SnippetAcceptingContext
{
    /**
     * @var bool
     */
    protected static $applyFixtures = false;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var User
     */
    private $user;

    /**
     * @var \LogicException
     */
    private $exception;

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
        $this->user->setState(User::STATE_APPROVED);
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

    /**
     * @When I try to generate a uuid with wrong entity manager
     */
    public function iTryToGenerateAUuidWithWrongEntityManager()
    {
        try {
            $uuid = new UUID();
            $uuid->generateUUIDForEntity(new EmptyEntityManager(), new \stdClass());
        } catch (\LogicException $ex) {
            $this->exception = $ex;
        }
    }

    /**
     * @Then I should get an error
     */
    public function iShouldGetAnError()
    {
        Assertion::notNull($this->exception);
    }
}
