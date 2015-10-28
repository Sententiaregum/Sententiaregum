<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Behat;

use AppBundle\Model\User\User;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\DomCrawler\Crawler;

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
        $this->assertTrue(isset($this->response['id']), 'Unsuccessful registration!');
    }

    /**
     * @Then I should have gotten an activation key in order to approve my account
     */
    public function iShouldHaveGottenAnActivationKeyInOrderToApproveMyAccount()
    {
        $repository = $this->getRepository('Account:User');
        $user       = $repository->findOneBy(['username' => $this->username]);

        $this->assertFalse(empty($user->getActivationKey()), 'Missing activation key!');
        $this->assertNotEquals(User::STATE_APPROVED, $user->getState(), 'User not approved!');
    }

    /**
     * @Then I should have an activation email
     */
    public function iShouldHaveAnActivationEmail()
    {
        $client = $this->recentClient;
        /** @var \Symfony\Bundle\SwiftMailerBundle\DataCollector\MessageDataCollector $mailCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertEquals(1, $mailCollector->getMessageCount(), 'Expected one email to be sent!');

        /** @var \Swift_Message $message */
        $message = $mailCollector->getMessages()[0];
        $this->assertEquals($message->getSubject(), 'Sententiaregum Notifications', 'Invalid subject on activation mail');
        $this->assertEquals(key($message->getTo()), 'sententiaregum@sententiaregum.dev', 'Invalid mailer target!');

        $crawler = new Crawler();
        $crawler->addContent($message->getChildren()[1]->getBody());

        $this->assertCount(2, $message->getChildren(), 'Every message requires a text and a html child!');
        $this->assertEquals(1, $crawler->filter('#n-ac-l-p')->count(), 'Invalid amount of confirmation link nodes!');
        $this->assertNotEquals(0, preg_match('/!\/activate\/(.*)/', $message->getChildren()[0]->getBody()), 'No text link found!');
    }

    /**
     * @When I enter this api key in order to approve the recently created account
     */
    public function iEnterThisApiKeyInOrderToApproveTheRecentlyCreatedAccount()
    {
        $user = $this->getRegisteredUser();

        $query = http_build_query(['username' => $user->getUsername(), 'activation_key' => $user->getActivationKey()]);
        $this->assertFalse(empty($user->getActivationKey()), 'Missing activation key on current user!');
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
        $this->assertTrue(isset($this->response['errors'][$arg2]), sprintf('Missing errors for %s in response!', $arg2));
        $this->assertTrue(in_array($arg1, $this->response['errors'][$arg2]), sprintf('Missing message "%s" on property "%s"!', $arg1, $arg2));
    }

    /**
     * @Then I should see suggestions for my username
     */
    public function iShouldSeeSuggestionsForMyUsername()
    {
        $this->assertTrue(array_key_exists('name_suggestions', $this->response));
    }

    /**
     * @Then I wait more than two hours
     */
    public function iWaitMoreThanTwoHours()
    {
        $user = $this->getRegisteredUser();

        $redis = $this->getContainer()->get('snc_redis.pending_activations');
        $redis->del(sprintf('activation_%s', $user->getActivationKey()));

        $pending       = $user->getPendingActivation();
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

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
        $this->assertFalse(empty($user->getActivationKey()), 'Missing activation key on current user!');
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
