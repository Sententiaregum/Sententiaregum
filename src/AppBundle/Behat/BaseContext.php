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

use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\DataFixtures\ORM\UserFixture;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Ma27\ApiKeyAuthenticationBundle\Security\ApiKeyAuthenticator;

/**
 * Base context that contains basic features of every behat context.
 */
abstract class BaseContext extends BehatAssert implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var string
     */
    protected static $managerName = 'default';

    /**
     * @var \Doctrine\Common\DataFixtures\FixtureInterface
     */
    protected static $fixtures = [];

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $recentClient;

    /** @BeforeScenario */
    public function loadDataFixtures()
    {
        $registry = $this->getContainer()->get('doctrine');
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $registry->getManager(self::$managerName);

        $purger   = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);

        $executor->execute(array_merge([new RoleFixture(), new UserFixture()], self::$fixtures));
    }

    /** @AfterScenario */
    public function tearDown()
    {
        $this->apiKey       = null;
        $this->recentClient = null;
    }

    /**
     * Performs a request to the symfony application and returns the response.
     *
     * @param string   $method
     * @param string   $uri
     * @param mixed[]  $parameters
     * @param bool     $expectSuccess
     * @param string[] $headers
     * @param mixed[]  $files
     * @param int      $expectedStatus
     * @param bool     $toJson
     * @param string   $apiKey
     * @param bool     $disableAssertions
     *
     * @throws \Exception If the json decode did not work
     *
     * @return string[]
     */
    protected function performRequest(
        $method,
        $uri,
        array $parameters = [],
        $expectSuccess = true,
        array $headers = [],
        array $files = [],
        $expectedStatus = 200,
        $toJson = true,
        $apiKey = null,
        $disableAssertions = false
    ) {
        if (null !== $apiKey || null !== $this->apiKey) {
            $headers[ApiKeyAuthenticator::API_KEY_HEADER] = $apiKey ?: $this->apiKey;
        }

        $headers = array_combine(
            array_map(function ($headerName) {
                return sprintf('HTTP_%s', $headerName);
            }, array_keys($headers)),
            array_values($headers)
        );

        /** @var \Symfony\Bundle\FrameworkBundle\Client $client */
        $client = $this->getContainer()->get('test.client');
        $client->enableProfiler();

        $client->request($method, $uri, $parameters, $files, $headers);
        $response = $client->getResponse();

        if (!$disableAssertions) {
            $status = $response->getStatusCode();
            if ($expectSuccess) {
                $this->assertTrue($status < 200 || $status > 399, sprintf('Expected success, but got error code "%d"', $status));
            } else {
                $this->assertTrue($status < 400 || $status > 599, sprintf('Expected failures, but got success code "%d"', $status));
            }

            $this->assertEquals($expectedStatus, $status, sprintf('Expected code "%d", but got "%d"!', $expectedStatus, $status));
        }

        $this->recentClient = $client;

        if ($toJson) {
            $content = $response->getContent();
            if (empty($content)) {
                $raw = null;
            } else {
                $raw = json_decode($content, true);
                $this->assertEquals(
                    JSON_ERROR_NONE,
                    json_last_error(),
                    sprintf('Malformatted json (%s) responded from uri "%s" with method "%s"!', $content, $uri, $method)
                );
            }

            return $raw;
        }

        return $response;
    }

    /**
     * Authenticates a user and returns the api key.
     *
     * @param string $username
     * @param string $password
     *
     * @throws \Exception If the authentication failed
     *
     * @return string
     */
    protected function authenticate($username, $password)
    {
        $response = $this->performRequest('POST', '/api/api-key.json', ['username' => $username, 'password' => $password]);

        return $this->apiKey = $response['apiKey'];
    }

    /**
     * Gets an entity manager.
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager(self::$managerName);
    }

    /**
     * Gets a repository by its name.
     *
     * @param string $entity
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($entity)
    {
        return $this->getEntityManager()->getRepository($entity);
    }
}
