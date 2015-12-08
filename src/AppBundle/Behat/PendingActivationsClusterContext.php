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

use AppBundle\Model\User\PendingActivation;
use AppBundle\Model\User\User;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Doctrine\ORM\Id\UuidGenerator;

/**
 * Context for the pending activations cluster functionality.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class PendingActivationsClusterContext extends BaseContext implements SnippetAcceptingContext
{
    /**
     * @var bool
     */
    protected static $applyFixtures = false;

    /**
     * @var bool
     */
    private $result;

    /**
     * @var string
     */
    private $key;

    /**
     * @Given there's an activation key stored in redis
     */
    public function thereSAnActivationKeyStoredInRedis()
    {
        /** @var \AppBundle\Model\User\Registration\Generator\ActivationKeyCodeGeneratorInterface $key */
        $key = $this->getContainer()->get('app.user.registration.activation_key_generator')->generate();

        $activation = new PendingActivation();
        $activation->setId((new UuidGenerator())->generate($this->getEntityManager(), $activation));
        $activation->setActivationDate(new \DateTime());
        $user = User::create('Ma27-2', '123456', 'ma27-2@sententiaregum.dev');
        $user->setId($this->getContainer()->get('app.doctrine.uuid')->generateUUIDForEntity($this->getEntityManager(), $user));
        $user->setPendingActivation($activation);
        $user->setActivationKey($key);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $this->getContainer()->get('app.redis.cluster.approval')->attachNewApproval($key);

        $this->key = $key;
    }

    /**
     * @When I try to approve it
     */
    public function iTryToApproveIt()
    {
        /** @var \AppBundle\Redis\PendingActivationsCluster $cluster */
        $cluster      = $this->getContainer()->get('app.redis.cluster.approval');
        $this->result = $cluster->checkApprovalByUser(
            $this->getEntityManager()->getRepository('Account:User')->findOneBy(['username' => 'Ma27-2'])
        );
    }

    /**
     * @Then the key should be marked as approvable
     */
    public function theKeyShouldBeMarkedAsApprovable()
    {
        Assertion::true($this->result);
    }

    /**
     * @Given the activation key is expired
     */
    public function theActivationKeyIsExpired()
    {
        $this->theKeyWasRemovedFromRedisDueToServerIssues();

        $user = $this->getEntityManager()->getRepository('Account:User')->findOneBy(['username' => 'Ma27-2']);
        $user->setPendingActivation(
            (new PendingActivation())
                ->setActivationDate(new \DateTime('-6 hours'))
                ->setId((new UuidGenerator())->generate($this->getEntityManager(), $user->getPendingActivation()))
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @Then the key should not be approvable
     */
    public function theKeyShouldNotBeApprovable()
    {
        Assertion::false($this->result);
    }

    /**
     * @Given the key was removed from redis due to server issues
     */
    public function theKeyWasRemovedFromRedisDueToServerIssues()
    {
        /** @var \Predis\Client $redis */
        $redis = $this->getContainer()->get('snc_redis.pending_activations');

        $redis->del(['activation:'.$this->key]);
    }
}
