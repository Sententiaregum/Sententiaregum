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

use AppBundle\Command\PurgeOutdatedPendingActivationsCommand;
use AppBundle\Model\User\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class PurgeOutdatedPendingActivationsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testPurgeUsers()
    {
        $cmd    = new PurgeOutdatedPendingActivationsCommand();
        $tester = new CommandTester($this->createCommandApp($cmd)->find('sententiaregum:purge:pending-activations'));

        $tester->execute([]);
        $display = $tester->getDisplay();

        $this->assertSame('Successfully purged 2 pending activations'.PHP_EOL, $display);
    }

    private function createCommandApp(ContainerAwareCommand $command)
    {
        $application = new Application($this->getKernel());
        $application->add($command);

        return $application;
    }

    /**
     * @return KernelInterface
     */
    private function getKernel()
    {
        $kernel = $this->getMock(KernelInterface::class);
        $kernel
            ->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->getContainer());

        return $kernel;
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container
            ->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['doctrine', 1, $this->getDoctrine()],
            ]);

        return $container;
    }

    /**
     * @return ManagerRegistry
     */
    private function getDoctrine()
    {
        $repository = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);
        $repository
            ->expects($this->any())
            ->method('deletePendingActivationsByDate')
            ->willReturn(2);

        $registry = $this->getMock(ManagerRegistry::class);
        $registry
            ->expects($this->any())
            ->method('getRepository')
            ->with('Account:User')
            ->willReturn($repository);

        return $registry;
    }
}
