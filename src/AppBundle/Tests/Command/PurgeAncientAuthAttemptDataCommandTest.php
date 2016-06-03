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

namespace AppBundle\Tests\Command;

use AppBundle\Command\PurgeAncientAuthAttemptDataCommand;
use AppBundle\Model\User\UserRepository;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PurgeAncientAuthAttemptDataCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testPurgeAuthAttemptData()
    {
        $repoMock = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);
        $repoMock->expects($this->once())
            ->method('deleteAncientAttemptData')
            ->with(new \DateTime('-6 months'))
            ->willReturn(5);

        $application = new Application();
        $command     = new PurgeAncientAuthAttemptDataCommand($repoMock);

        $application->add($command);

        $commandTester = new CommandTester($application->find('sententiaregum:purge:ancient-auth-attempt-log-data'));
        $commandTester->execute(['command' => 'sententiaregum:purge:ancient-auth-attempt-log-data']);

        $display = $commandTester->getDisplay();
        $code    = $commandTester->getStatusCode();

        $this->assertSame(0, $code);
        $this->assertRegExp('/Successfully purged 5 ancient auth models\./', $display);
    }
}
