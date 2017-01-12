<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Tests\Acceptance\Integration\Database;

use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\Model\User\Role;
use AppBundle\Tests\Acceptance\AbstractIntegrationContext;
use Assert\Assertion;
use Behat\Gherkin\Node\TableNode;

/**
 * Feature context for the data fixture basic behavior.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class SimpleFixtureLoadingContext extends AbstractIntegrationContext
{
    /**
     * @var bool
     */
    protected static $applyFixtures = false;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var string[]
     */
    private $data = [
        'called' => null,
        'output' => [],
    ];

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var string
     */
    private $directoryResult;

    /**
     * @When I apply fixtures
     */
    public function applyFixtures(): void
    {
        /** @var \AppBundle\Service\Doctrine\DataFixtures\ConfigurableFixturesLoader $loader */
        $loader = $this->getContainer()->get('app.doctrine.fixtures_loader');

        $loader->applyFixtures([RoleFixture::class], $this->callback);
    }

    /**
     * @Then I should be able to fetch them from the database
     */
    public function ensureAppropriateAppliance(): void
    {
        $em = $this->getEntityManager();
        foreach (['ROLE_USER', 'ROLE_ADMIN'] as $role) {
            Assertion::isInstanceOf($em->getRepository('Account:Role')->findOneBy(['role' => $role]), Role::class);
        }
    }

    /**
     * @When I have a logging callback defined
     */
    public function defineLoggerForFixtureAppliance(): void
    {
        $that           = &$this;
        $this->callback = function ($message) use ($that) {
            $that->data['called']   = true;
            $that->data['output'][] = $message;
        };
    }

    /**
     * @Then the callback should be called
     */
    public function ensureLoggerWasCalled(): void
    {
        Assertion::true($this->data['called']);
    }

    /**
     * @Then the log messages should be shown
     */
    public function checkLogMessage(): void
    {
        list($line0, $line1) = $this->data['output'];

        Assertion::startsWith($line0, 'purging database');
        Assertion::endsWith($line1, RoleFixture::class);
    }

    /**
     * @When I apply an invalid fixture
     */
    public function applyUnknownFixtures(): void
    {
        /** @var \AppBundle\Service\Doctrine\DataFixtures\ConfigurableFixturesLoader $loader */
        $loader = $this->getContainer()->get('app.doctrine.fixtures_loader');

        try {
            $loader->applyFixtures(['Invalid\\Class']);
        } catch (\Exception $exception) {
            $this->exception = $exception;
        }
    }

    /**
     * @Then I should get an error
     */
    public function checkError(): void
    {
        Assertion::notNull($this->exception);
        Assertion::isInstanceOf($this->exception, \InvalidArgumentException::class);
    }

    /**
     * @When I load production fixtures from the DataFixtures\/ORM directory inside AppBundle
     */
    public function loadProdFixturesFromDir(): void
    {
        /** @var \AppBundle\Service\Doctrine\DataFixtures\ConfigurableFixturesLoader $loader */
        $loader = $this->getContainer()->get('app.doctrine.fixtures_loader');

        $this->directoryResult = $loader->loadProductionFixturesFromDirectory(__DIR__.'/../../../../DataFixtures/ORM');
    }

    /**
     * @Then I should see the following fixtures
     */
    public function checkFixtures(TableNode $table): void
    {
        $classes = array_map(
            function ($array) {
                return $array['class'];
            },
            $table->getHash()
        );

        $result = array_map(
            function ($array) {
                return get_class($array);
            },
            $this->directoryResult
        );

        foreach ($classes as $row) {
            Assertion::inArray($row, $result);
        }
    }
}
