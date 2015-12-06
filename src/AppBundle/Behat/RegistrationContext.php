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
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Context for the registration behavior.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class RegistrationContext extends BaseContext implements SnippetAcceptingContext
{
    /**
     * @var mixed[]
     */
    private $response;

    /**
     * @var string
     */
    private $username;

    /**
     * @When I send a registration request with the following credentials:
     *
     * @param TableNode $table
     */
    public function iSendARegistrationRequestWithTheFollowingCredentials(TableNode $table)
    {
        $row            = $table->getRow(1);
        $this->username = $row[0];

        $this->response = $this->performRequest(
            'POST',
            '/api/users.json',
            ['username' => $row[0], 'password' => $row[1], 'email' => $row[2], 'locale' => $row[3]],
            true,
            [],
            [],
            200,
            true,
            null,
            true
        );
    }

    /**
     * @Then I should have an account
     */
    public function iShouldHaveAnAccount()
    {
        Assertion::keyIsset($this->response, 'id');
    }

    /**
     * @Then I should have gotten an activation key in order to approve my account
     */
    public function iShouldHaveGottenAnActivationKeyInOrderToApproveMyAccount()
    {
        $repository = $this->getRepository('Account:User');
        $user       = $repository->findOneBy(['username' => $this->username]);

        Assertion::notEmpty($user->getActivationKey());
        Assertion::notEq(User::STATE_APPROVED, $user->getState());
    }

    /**
     * @Then I should have an activation email
     */
    public function iShouldHaveAnActivationEmail()
    {
        $client = $this->recentClient;
        /** @var \Symfony\Bundle\SwiftMailerBundle\DataCollector\MessageDataCollector $mailCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        Assertion::eq(1, $mailCollector->getMessageCount());

        /** @var \Swift_Message $message */
        $message = $mailCollector->getMessages()[0];
        Assertion::eq($message->getSubject(), 'Sententiaregum Notifications');
        Assertion::eq(key($message->getTo()), 'sententiaregum@sententiaregum.dev');

        $crawler = new Crawler();
        $crawler->addContent($message->getChildren()[1]->getBody());

        Assertion::count($message->getChildren(), 2);
        Assertion::eq(1, $crawler->filter('#n-ac-l-p')->count());
        Assertion::notEq(0, preg_match('/!\/activate\/(.*)/', $message->getChildren()[0]->getBody()));
    }

    /**
     * @When I enter this api key in order to approve the recently created account
     */
    public function iEnterThisApiKeyInOrderToApproveTheRecentlyCreatedAccount()
    {
        $user = $this->getRegisteredUser();

        $query = http_build_query(['username' => $user->getUsername(), 'activation_key' => $user->getActivationKey()]);
        Assertion::false(empty($user->getActivationKey()));
        $this->response = $this->performRequest(
            'PATCH',
            sprintf('/api/users/activate.json?%s', $query),
            [],
            true,
            [],
            [],
            204
        );
    }

    /**
     * @Then I should be able to login
     */
    public function iShouldBeAbleToLogin()
    {
        $this->authenticate('sententiaregum', '123456');
    }

    /**
     * @Then I should see :arg1 for property :arg2
     */
    public function iShouldSee($arg1, $arg2)
    {
        Assertion::keyIsset($this->response['errors'], $arg2);
        Assertion::inArray($arg1, $this->response['errors'][$arg2]);
    }

    /**
     * @Then I should see suggestions for my username
     */
    public function iShouldSeeSuggestionsForMyUsername()
    {
        Assertion::keyExists($this->response, 'name_suggestions');
    }

    /**
     * @Then I wait more than two hours
     */
    public function iWaitMoreThanTwoHours()
    {
        $user = $this->getRegisteredUser();

        $redis = $this->getContainer()->get('snc_redis.pending_activations');
        $redis->del(sprintf('activation:%s', $user->getActivationKey()));

        $pending       = $user->getPendingActivation();
        $entityManager = $this->getEntityManager();

        $pending->setActivationDate(new \DateTime('-3 hours'));
        $entityManager->persist($pending);
        $entityManager->flush();
    }

    /**
     * @When I try to enter the activation key
     */
    public function iTryToEnterTheActivationKey()
    {
        $user = $this->getRegisteredUser();

        $query = http_build_query(['username' => $user->getUsername(), 'activation_key' => $user->getActivationKey()]);
        Assertion::notEmpty($user->getActivationKey());
        $this->response = $this->performRequest(
            'PATCH',
            sprintf('/api/users/activate.json?%s', $query),
            [],
            false,
            [],
            [],
            403
        );
    }

    /**
     * @Then the activation should be declined
     */
    public function theActivationShouldBeDeclined()
    {
        $this->authenticate('sententiaregum', '123456', false);
    }

    private function getRegisteredUser()
    {
        $repository = $this->getRepository('Account:User');
        $user       = $repository->findOneBy(['username' => $this->username]);

        return $user;
    }
}
