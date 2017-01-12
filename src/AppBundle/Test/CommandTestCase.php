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

namespace AppBundle\Test;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * API to unittest commands.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
abstract class CommandTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Executes a symfony command and returns the testing instance.
     *
     * @param Command $command
     * @param array   $input
     *
     * @return CommandTester
     */
    public function executeCommand(Command $command, array $input = []): CommandTester
    {
        $name = $command->getName();

        $application = new Application();
        $application->add($command);

        $tester = new CommandTester($application->find($name));
        $tester->execute(array_merge(['command' => $name], $input));

        return $tester;
    }
}
