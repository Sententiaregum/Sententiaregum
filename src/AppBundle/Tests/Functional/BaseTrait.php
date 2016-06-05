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

namespace AppBundle\Tests\Functional;

use Assert\Assertion;
use Behat\Symfony2Extension\Context\KernelDictionary;

/**
 * Basic trait to separate the AppContext from the BaseContext.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
trait BaseTrait
{
    use KernelDictionary;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $recentClient;

    /**
     * Gets an entity manager.
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Gets a repository by its name.
     *
     * @param string $entity
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository($entity)
    {
        return $this->getEntityManager()->getRepository($entity);
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
    public function performRequest(
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
        if (null !== $apiKey || null !== AppContext::$apiKey) {
            $headers['X-API-KEY'] = $apiKey ?: AppContext::$apiKey;
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
                Assertion::greaterOrEqualThan($status, 200);
                Assertion::lessOrEqualThan($status, 399);
            } else {
                Assertion::greaterOrEqualThan($status, 400);
                Assertion::lessOrEqualThan($status, 599);
            }

            Assertion::same($expectedStatus, $status);
        }

        $this->recentClient = $client;

        if ($toJson) {
            $content = $response->getContent();
            if (empty($content)) {
                $raw = null;
            } else {
                $raw = json_decode($content, true);
                Assertion::same(JSON_ERROR_NONE, json_last_error());
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
     * @param bool   $expectSuccess
     *
     * @throws \Exception If the authentication failed
     *
     * @return string
     */
    public function authenticate($username, $password, $expectSuccess = true)
    {
        $response = $this->performRequest(
            'POST',
            '/api/api-key.json',
            ['login' => $username, 'password' => $password],
            $expectSuccess,
            [],
            [],
            $expectSuccess ? 200 : 401
        );

        if ($expectSuccess) {
            return $response['apiKey'];
        } else {
            Assertion::false(isset($response['apiKey']));

            return false;
        }
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    public function getRecentClient()
    {
        return $this->recentClient;
    }
}
