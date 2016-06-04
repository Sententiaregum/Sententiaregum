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

namespace AppBundle\Test;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * API to unittest commands.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
abstract class CommandTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Executes a symfony2 command and returns the testing instance.
     *
     * @param Command $command
     * @param array   $input
     *
     * @return CommandTester
     */
    public function executeCommand(Command $command, array $input = [])
    {
        $name = $command->getName();

        $application = new Application();
        $application->add($command);

        $tester = new CommandTester($application->find($name));
        $tester->execute(array_merge(['command' => $name], $input));

        return $tester;
    }
}
