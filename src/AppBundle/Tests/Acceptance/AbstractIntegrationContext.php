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

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * AbstractIntegrationContext.
 *
 * Abstract helper class for all integration tests.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
abstract class AbstractIntegrationContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * Executor which runs a command.
     *
     * @param string $name
     * @param array  $args
     *
     * @return CommandTester
     */
    protected function executeCommand(string $name, array $args = []): CommandTester
    {
        $application = new Application($this->getKernel());
        $tester      = new CommandTester($application->get($name));

        $tester->execute($args, ['interactive' => false]);

        return $tester;
    }

    /**
     * Simple shortcut to get the entity manager.
     *
     * @return EntityManagerInterface
     */
    protected function getEntityManager():EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }
}
