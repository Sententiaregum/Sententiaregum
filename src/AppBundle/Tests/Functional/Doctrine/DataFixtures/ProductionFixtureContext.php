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

namespace AppBundle\Tests\Functional\Doctrine\DataFixtures;

use AppBundle\Command\LoadCustomFixturesCommand;
use AppBundle\Tests\Functional\FixtureLoadingContext;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Generic fixtures loader.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ProductionFixtureContext extends FixtureLoadingContext implements SnippetAcceptingContext
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
    public function iRunTheProductionFixturesLoader()
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
    public function iShouldSeeTheLoggingMessages()
    {
        $display = $this->display;

        Assertion::contains($display, 'purging database');
        Assertion::contains($display, 'AppBundle\DataFixtures\ORM\RoleFixture');
        Assertion::contains($display, 'AppBundle\DataFixtures\ORM\AdminFixture');
    }

    /**
     * @Then the role and admin fixtures should be applied
     */
    public function theRoleAndAdminFixturesShouldBeApplied()
    {
        $em = $this->getEntityManager();

        $roleRepository = $em->getRepository('Account:Role');
        $userRepository = $em->getRepository('Account:User');

        Assertion::notNull($roleRepository->findOneBy(['role' => 'ROLE_USER']));
        Assertion::notNull($roleRepository->findOneBy(['role' => 'ROLE_ADMIN']));
        Assertion::notNull($userRepository->findOneBy(['username' => 'admin']));
    }
}
