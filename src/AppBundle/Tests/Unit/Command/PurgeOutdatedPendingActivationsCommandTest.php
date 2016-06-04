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

namespace AppBundle\Tests\Unit\Command;

use AppBundle\Command\PurgeOutdatedPendingActivationsCommand;
use AppBundle\Model\User\UserRepository;
use AppBundle\Test\CommandTestCase;

class PurgeOutdatedPendingActivationsCommandTest extends CommandTestCase
{
    public function testPurgeData()
    {
        $repository = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('deletePendingActivationsByDate')
            ->willReturn(5);

        $command = new PurgeOutdatedPendingActivationsCommand($repository);
        $result  = $this->executeCommand($command);

        $this->assertSame(0, $result->getStatusCode());
        $this->assertRegExp('/Successfully purged 5 pending activations./', $result->getDisplay());
    }
}
