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

namespace AppBundle\Tests\Unit\Test;

use AppBundle\Test\CommandTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandTestCaseTest extends \PHPUnit_Framework_TestCase
{
    public function testRunsCommand()
    {
        /** @var CommandTestCase $tester */
        $tester = $this->getMockForAbstractClass(CommandTestCase::class);
        $result = $tester->executeCommand(new TestCommand());

        $this->assertSame(0, $result->getStatusCode());
        $this->assertRegExp('/Something happened/', $result->getDisplay());
    }
}

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('foo:bar');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Something happened');

        return 0;
    }
}
