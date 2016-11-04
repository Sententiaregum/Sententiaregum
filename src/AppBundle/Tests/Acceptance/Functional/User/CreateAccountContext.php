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

namespace AppBundle\Tests\Acceptance\Functional\User;

use AppBundle\Tests\Acceptance\ContextHelper;
use Assert\Assertion;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Context for the registration behavior.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class CreateAccountContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var \AppBundle\Tests\Acceptance\ApiContext
     */
    private $apiContext;

    /** @BeforeScenario */
    public function connectToAPiContext(BeforeScenarioScope $scope)
    {
        $this->apiContext = ContextHelper::connectToAPIContext($scope);

        $this->apiContext->enableProfiling();
    }

    /** @AfterScenario */
    public function dropApiContext()
    {
        $this->apiContext = null;
    }

    /**
     * @Then /^I should have an account with name "(.*)"/
     *
     * @param string $name
     */
    public function ensureAccountExists(string $name)
    {
        Assertion::eq(
            $this->apiContext->getResponse()['id'],
            $this->getContainer()->get('app.repository.user')->findOneBy(['username' => $name])->getId()
        );
    }

    /**
     * @Then /^I should've gotten an email$/
     */
    public function checkEmail()
    {
        /** @var \Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector $profile */
        $profile = $this->apiContext->getProfile()->getCollector('swiftmailer');

        Assertion::eq(1, $profile->getMessageCount());

        /** @var \Swift_Message $message */
        $message = $profile->getMessages()[0];

        // user registers as "de", so email should be in german
        Assertion::eq($message->getSubject(), 'Benachrichtigungen von Sententiaregum');
        Assertion::eq(key($message->getTo()), 'sententiaregum@sententiaregum.dev');

        $crawler = new Crawler();
        $crawler->addContent($message->getChildren()[1]->getBody());

        Assertion::count($message->getChildren(), 2);
        Assertion::eq(1, $crawler->filter('#n-ac-l-p')->count());
        Assertion::notEq(0, preg_match('/\/activate\/(.*)/', $message->getChildren()[0]->getBody()));
    }

    /**
     * @Given /^I created an account with username "(.*)"$/
     *
     * @param string $username
     */
    public function createAccount(string $username)
    {
        $this->getContainer()->get('test.client')->request(
            'POST',
            '/api/users.json',
            [
                'username'      => $username,
                'password'      => '123456',
                'email'         => sprintf('%s@gmx.de', $username),
                'locale'        => 'en',
                'recaptchaHash' => 'hash-val',
            ]
        );
    }

    /**
     * @Then /^I should be able to login as "(.*)"$/
     *
     * @param string $username
     */
    public function checkAbilityToLogin(string $username)
    {
        $this->apiContext->authenticate($username, '123456');
    }

    /**
     * @Given /^I should have "(.*)" as activation key$/
     *
     * @param string $key
     */
    public function modifyTestApiKey(string $key)
    {
        $connection = $this->getContainer()->get('database_connection');

        $query = $connection->prepare('UPDATE `User` SET `pendingActivation_key` = :key WHERE `username` = "sententiaregum"');
        $query->bindParam('key', $key);
        $query->execute();

        $this->getContainer()->get('doctrine.orm.default_entity_manager')->clear();
    }

    /**
     * @Then /^I should see '(.*)' for property "(.*)"$/
     *
     * @param string $error
     * @param string $propertyPath
     */
    public function checkError(string $error, string $propertyPath)
    {
        $response = $this->apiContext->getResponse();

        Assertion::keyIsset($response['errors'], $propertyPath);
        Assertion::inArray($error, $response['errors'][$propertyPath]['en']);
    }

    /**
     * @Then /^I should see suggestions for my username$/
     */
    public function checkNameSuggestions()
    {
        Assertion::keyIsset($this->apiContext->getResponse(), 'name_suggestions');
    }

    /**
     * @Given /^I wait more than two hours$/
     */
    public function simulateWaitingAndExpiredKey()
    {
        $bind  = (new \DateTime('-3 hours'))->format('Y-m-d H:i:s');
        $query = $this->getContainer()->get('database_connection')->prepare('UPDATE `User` SET `pendingActivation_activation_date` = :date WHERE `username` = "sententiaregum"');
        $query->bindParam(':date', $bind);
        $query->execute();

        $this->getContainer()->get('doctrine.orm.default_entity_manager')->clear();
    }
}
