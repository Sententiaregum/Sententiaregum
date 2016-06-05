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
use AppBundle\Test\CommandTestCase;

class PurgeAncientAuthAttemptDataCommandTest extends CommandTestCase
{
    public function testPurgeAuthAttemptData()
    {
        $repoMock = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);
        $repoMock->expects($this->once())
            ->method('deleteAncientAttemptData')
            ->willReturn(5);

        $command       = new PurgeAncientAuthAttemptDataCommand($repoMock);
        $commandTester = $this->executeCommand($command);

        $display = $commandTester->getDisplay();
        $code    = $commandTester->getStatusCode();

        $this->assertSame(0, $code);
        $this->assertRegExp('/Successfully purged 5 ancient auth models\./', $display);
    }
}
