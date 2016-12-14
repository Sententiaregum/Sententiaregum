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

namespace AppBundle\Tests\Acceptance\Integration\Database;

use AppBundle\Command\LoadCustomFixturesCommand;
use AppBundle\Tests\Acceptance\AbstractIntegrationContext;
use Assert\Assertion;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Generic fixtures loader.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ProductionFixtureApplianceContext extends AbstractIntegrationContext
{
    /**
     * @var bool
     */
    protected static $applyFixtures = false;

    /**
     * @var string
     */
    private $display;

    /**
     * @When I run the production fixtures loader
     */
    public function runProductionFixtureAppliance(): void
    {
        $cmdApplication = new Application($this->getKernel());
        $cmdApplication->add(new LoadCustomFixturesCommand());

        $tester = new CommandTester($cmdApplication->find('sententiaregum:fixtures:production'));
        $tester->execute(['bundles' => ['AppBundle'], '--no-interaction'], ['interactive' => false]);
        $this->display = $tester->getDisplay();
    }

    /**
     * @Then I should see the logging messages
     */
    public function ensureLoggingMessages(): void
    {
        $display = $this->display;

        Assertion::contains($display, 'purging database');
        Assertion::contains($display, 'AppBundle\DataFixtures\ORM\RoleFixture');
        Assertion::contains($display, 'AppBundle\DataFixtures\ORM\AdminFixture');
    }

    /**
     * @Then the role and admin fixtures should be applied
     */
    public function ensureAppliedFixtures(): void
    {
        $em = $this->getEntityManager();

        $roleRepository = $em->getRepository('Account:Role');
        $userRepository = $em->getRepository('Account:User');

        Assertion::notNull($roleRepository->findOneBy(['role' => 'ROLE_USER']));
        Assertion::notNull($roleRepository->findOneBy(['role' => 'ROLE_ADMIN']));
        Assertion::notNull($userRepository->findOneBy(['username' => 'admin']));
    }
}
