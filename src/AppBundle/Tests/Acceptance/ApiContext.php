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

namespace AppBundle\Tests\Acceptance;

use AppBundle\DataFixtures\ORM\AdminFixture;
use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\DataFixtures\ORM\UserFixture;
use Assert\Assertion;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * ApiContext.
 *
 * Basic behat context which provides main functionality for API testing based on assertions.
 * This context is capable at configuring a full API request (with authentication if needed) and
 * to assert against all the required data.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ApiContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var mixed[]
     */
    private $requestPayload = [];

    /**
     * @var array
     */
    private $response;

    /**
     * @var int
     */
    private $responseCode;

    /**
     * @var bool
     */
    private $profilerEnabled = false;

    /**
     * @var \Symfony\Component\HttpKernel\Profiler\Profile
     */
    private $profiler;

    /** @BeforeScenario */
    public function prepare()
    {
        $container = $this->getContainer();

        // loads the client into the context
        $this->client = $container->get('test.client');

        // re-applies the fixtures
        $container->get('app.doctrine.fixtures_loader')->applyFixtures([
            UserFixture::class,
            RoleFixture::class,
            AdminFixture::class,
        ]);
    }

    /** @AfterScenario */
    public function cleanUp()
    {
        $this->client          = null;
        $this->apiKey          = null;
        $this->response        = null;
        $this->requestPayload  = [];
        $this->responseCode    = null;
        $this->profiler        = null;
        $this->profilerEnabled = false;
    }

    /**
     * @Given /^I'm authenticated as "(.*)" with password "(.*)"$/
     *
     * @param string $username
     * @param string $password
     */
    public function authenticate(string $username, string $password)
    {
        $this->client->request('POST', '/api/api-key.json', ['login' => $username, 'password' => $password]);
        $response = $this->client->getResponse();

        Assertion::eq(200, $response->getStatusCode(), sprintf(
            'Authentication with username "%s" and password "%s" failed!',
            $username,
            $password
        ));

        $this->apiKey = $this->decode($response->getContent())['apiKey'];
    }

    /**
     * @Given /^I have the following payload:/
     *
     * @param PyStringNode $payload
     */
    public function buildPayload(PyStringNode $payload)
    {
        $this->requestPayload = $this->decode($payload->getRaw());
    }

    /**
     * @When /^I submit a request to "(.*)"$/
     *
     * @param string $request
     */
    public function submitRequest(string $request)
    {
        list($method, $url) = $this->analyzeURLInput($request);

        if ($this->profilerEnabled) {
            $this->client->enableProfiler();
        }
        $this->client->request(
            $method,
            $url,
            $this->requestPayload,
            [],
            $this->apiKey ? ['HTTP_X-API-KEY' => $this->apiKey] : []
        );

        $response           = $this->client->getResponse();
        $this->response     = $this->decode($response->getContent());
        $this->responseCode = $response->getStatusCode();

        if ($this->profilerEnabled) {
            $this->profiler = $this->client->getProfile();
        }
    }

    /**
     * @Then /^I should get a response with the (\d+) status code$/
     *
     * @param int $statusCode
     */
    public function checkStatusCode(int $statusCode)
    {
        Assertion::eq($statusCode, $this->responseCode, sprintf(
            'Invalid status code after request! Expected code "%s", but got "%s"!',
            $statusCode,
            $this->responseCode
        ));
    }

    /**
     * @Then /^I should get "(.*)" for the response property path "(.*)"$/
     *
     * @param string $expected
     * @param string $propertyPath
     */
    public function ensureParameterValidity(string $expected, string $propertyPath)
    {
        $value = $this->evaluatePropertyPath($propertyPath);

        Assertion::eq($expected, $value);
    }

    /**
     * @Then /^I should get the following response:/
     *
     * @param TableNode $node
     */
    public function checkWithTableNode(TableNode $node)
    {
        $data = array_combine($node->getRow(0), $node->getRow(1));

        Assertion::eq($data, $this->response);
    }

    /**
     * Simple getter to access the response.
     *
     * In some cases other feature contexts need to know about the response and then it's helpful to
     * access it via this context.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Simple utility to enable request profiling during scenarios.
     */
    public function enableProfiling()
    {
        $this->profilerEnabled = true;
    }

    /**
     * Simple getter to access the profile.
     *
     * @return \Symfony\Component\HttpKernel\Profiler\Profile
     */
    public function getProfile()
    {
        return $this->profiler;
    }

    /**
     * Decodes a single json value.
     *
     * @param string $json
     *
     * @return array
     */
    private function decode(string $json): array
    {
        if (!$json) {
            return [];
        }

        $decoded = json_decode($json, true);

        Assertion::eq(JSON_ERROR_NONE, json_last_error(), sprintf(
            'JSON analysis for json value ("%s") failed due to the following error: %s',
            $json,
            json_last_error_msg()
        ));

        return $decoded;
    }

    /**
     * Evaluates a property path.
     *
     * @param string $propertyPath
     *
     * @return mixed
     */
    private function evaluatePropertyPath(string $propertyPath)
    {
        return PropertyAccess::createPropertyAccessor()->getValue($this->response, $propertyPath);
    }

    /**
     * Analyzes a request input which might look like "POST /api/create".
     *
     * @param string $request
     *
     * @return array
     */
    private function analyzeURLInput(string $request): array
    {
        $method = ($matched = (bool) preg_match('/^([\w]+) (\/(.*))$/', $request, $matches)) ? $matches[1] : 'GET';

        return [$method, $matched ? $matches[2] : $request];
    }
}
