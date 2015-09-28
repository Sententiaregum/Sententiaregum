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
use Behat\Behat\Tester\Exception\PendingException;
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
     * @When I send an registration request with the following credentials:
     *
     * @param TableNode $table
     */
    public function iSendAnRegistrationRequestWithTheFollowingCredentials(TableNode $table)
    {
        $row            = $table->getRow(1);
        $this->username = $row[0];

        $this->response = $this->performRequest(
            'POST',
            '/api/users.json',
            ['username' => $row[0], 'password' => $row[1], 'email' => $row[2], 'locale' => $row[3]]
        );
    }

    /**
     * @Then I should have an account
     */
    public function iShouldHaveAnAccount()
    {
        if (!isset($this->response['id'])) {
            throw new \Exception('Unsuccessful registration!');
        }
    }

    /**
     * @Then I should have gotten an activation key in order to approve my account
     */
    public function iShouldHaveGottenAnActivationKeyInOrderToApproveMyAccount()
    {
        $repository = $this->getRepository('User:User');
        $user       = $repository->findOneBy(['username' => $this->username]);

        if (!$user->getActivationKey() || User::STATE_APPROVED === $user->getState()) {
            throw new \Exception('Newly registered user must not be approved and must have an activation key!');
        }
    }

    /**
     * @Then I should have an activation email
     */
    public function iShouldHaveAnActivationEmail()
    {
        $client = $this->recentClient;
        /** @var \Symfony\Bundle\SwiftMailerBundle\DataCollector\MessageDataCollector $mailCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        if (1 !== $mailCollector->getMessageCount()) {
            throw new \Exception('Expected one email to be sent!');
        }

        /** @var \Swift_Message $message */
        $message = $mailCollector->getMessages()[0];

        if ($message->getSubject() !== 'Sententiaregum Notifications') {
            throw new \Exception('Invalid subject on activation email!');
        }

        if (key($message->getTo()) !== 'sententiaregum@sententiaregum.dev') {
            throw new \Exception('Invalid mailer target!');
        }

        $crawler = new Crawler();
        $crawler->addContent($message->getChildren()[1]->getBody());

        if (1 !== $crawler->filter('#n-ac-l-p')->count()) {
            throw new \Exception('Invalid amount of confirmation link nodes!');
        }
    }

    /**
     * @When I enter this api key in order to approve the recently created account
     */
    public function iEnterThisApiKeyInOrderToApproveTheRecentlyCreatedAccount()
    {
        throw new PendingException();
    }

    /**
     * @Then I should be able to login
     */
    public function iShouldBeAbleToLogin()
    {
        throw new PendingException();
    }
}
