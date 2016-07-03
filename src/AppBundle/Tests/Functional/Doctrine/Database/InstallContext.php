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

namespace AppBundle\Tests\Functional\Doctrine\Database;

use AppBundle\Model\User\User;
use AppBundle\Tests\Functional\BaseTrait;
use AppBundle\Tests\Functional\FixtureLoadingContext;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

/**
 * Context which checks the database installation behavior.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class InstallContext extends FixtureLoadingContext implements SnippetAcceptingContext
{
    use BaseTrait;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    private $tester;

    /**
     * @var \Exception
     */
    private $exception;

    /** @BeforeScenario @fixtures&&@database */
    public function recreateSchema()
    {
        $em   = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $all  = $em->getMetadataFactory()->getAllMetadata();

        $tool->dropSchema($all);
        $tool->createSchema($all);
    }

    /**
     * @Given the schema is dropped
     */
    public function theSchemaIsDropped()
    {
        $em   = $this->getEntityManager();
        $tool = new SchemaTool($em);

        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @When I apply the schema
     */
    public function iApplyTheSchema()
    {
        $this->tester = $this->executeCommand('sententiaregum:install:database');
    }

    /**
     * @Then I the tool should apply the DDL to the database
     */
    public function iTheToolShouldApplyTheDdlToTheDatabase()
    {
        $tool = new SchemaValidator($this->getEntityManager());
        Assertion::true($tool->schemaInSyncWithMetadata());

        Assertion::regex($this->tester->getDisplay(), '/Validated 1 manager, 1 needed schema appliance, 0 was in sync\./');
    }

    /**
     * @When I apply the schema with migrations
     */
    public function iApplyTheSchemaWithMigrations()
    {
        $this->tester = $this->executeCommand('sententiaregum:install:database', [
            '--strategy' => 'migrations',
        ]);
    }

    /**
     * @Then the migrations should be applied
     */
    public function theMigrationsShouldBeApplied()
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare(strtr(
            'SELECT COUNT(version) FROM :table',
            [
                ':table' => $this->getContainer()->getParameter('doctrine_migrations.table_name'),

            ]
        ));

        $stmt->execute();

        Assertion::eq(1, (int) $stmt->fetch()['COUNT(version)']);
        Assertion::regex($this->tester->getDisplay(), '/Validated 1 manager, 1 needed schema appliance, 0 was in sync\./');
    }

    /**
     * @When I apply production fixtures
     */
    public function iApplyProductionFixtures()
    {
        $this->tester = $this->executeCommand('sententiaregum:install:database', [
            '--apply-fixtures'      => true,
            '--production-fixtures' => true,
        ]);
    }

    /**
     * @When I apply all fixtures
     */
    public function iApplyAllFixtures()
    {
        $this->tester = $this->executeCommand('sententiaregum:install:database', [
            '--apply-fixtures' => true,
        ]);
    }

    /**
     * @Then the fixtures should be loaded
     */
    public function theFixturesShouldBeLoaded()
    {
        // ensure that some data is loaded
        $em = $this->getEntityManager();

        Assertion::notNull($em->getRepository('Account:Role')->findOneBy(['role' => 'ROLE_USER']));
        Assertion::notNull($em->getRepository('Account:User')->findOneBy(['username' => 'Ma27']));
    }

    /**
     * @Given the database is not empty
     */
    public function theDatabaseIsNotEmpty()
    {
        $user = User::create('testuser', '123456', 'testuser@gmail.com', new PhpPasswordHasher());

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @When I apply all fixtures with --append option
     */
    public function iApplyAllFixturesWithAppendOption()
    {
        $this->tester = $this->executeCommand('sententiaregum:install:database', [
            '--apply-fixtures' => true,
            '--append'         => true,
        ]);
    }

    /**
     * @Then the fixtures should be appended
     */
    public function theFixturesShouldBeAppended()
    {
        $em = $this->getEntityManager();

        Assertion::notNull($em->getRepository('Account:User')->findOneBy(['username' => 'testuser']));
        Assertion::notNull($em->getRepository('Account:Role')->findOneBy(['role' => 'ROLE_USER']));
    }

    /**
     * @When I apply using an invalid strategy
     */
    public function iApplyUsingAnInvalidStrategy()
    {
        try {
            $this->executeCommand('sententiaregum:install:database', [
                '--strategy' => 'invalid strategy',
            ]);
        } catch (\Exception $ex) {
            $this->exception = $ex;
        }
    }

    /**
     * @Then I should see an error from the installer
     */
    public function iShouldSeeAnErrorFromTheInstaller()
    {
        Assertion::eq($this->exception->getMessage(), 'The strategy must be either "migrations" or "schema-update"!');
    }

    /**
     * @Given the database schema is applied
     */
    public function theDatabaseSchemaIsApplied()
    {
        $this->iApplyTheSchema();
        $this->tester = null;
    }

    /**
     * @Then the process should be skipped
     */
    public function theProcessShouldBeSkipped()
    {
        Assertion::regex($this->tester->getDisplay(), '/Validated 1 manager, 0 needed schema appliance, 1 was in sync./');
    }

    /**
     * @When I apply using the --production-fixtures option and the --append option
     */
    public function iApplyUsingTheProductionFixturesOptionAndTheAppendOption()
    {
        try {
            $this->executeCommand('sententiaregum:install:database', [
                '--production-fixtures' => true,
            ]);
        } catch (\Exception $ex) {
            $this->exception = $ex;
        }
    }

    /**
     * @Then the appliance should be skipped
     */
    public function theApplianceShouldBeSkipped()
    {
        Assertion::eq($this->exception->getMessage(), 'The `--production-fixtures` option must not be set if the `--apply-fixtures` option is not present!');
    }
}
