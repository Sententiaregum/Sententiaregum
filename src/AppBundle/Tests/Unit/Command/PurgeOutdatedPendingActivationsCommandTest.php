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

namespace AppBundle\Tests\Unit\Command;

use AppBundle\Command\PurgeOutdatedPendingActivationsCommand;
use AppBundle\Model\User\UserWriteRepositoryInterface;
use AppBundle\Test\CommandTestCase;

class PurgeOutdatedPendingActivationsCommandTest extends CommandTestCase
{
    public function testPurgeData(): void
    {
        $repository = $this->createMock(UserWriteRepositoryInterface::class);
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
