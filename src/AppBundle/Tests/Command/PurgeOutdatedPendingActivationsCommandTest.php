<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
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
            ->will($this->returnValue($this->getContainer()));

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
            ->will($this->returnValueMap([
                ['doctrine', 1, $this->getDoctrine()],
            ]));

        return $container;
    }

    /**
     * @return ManagerRegistry
     */
    private function getDoctrine()
    {
        $repository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->any())
            ->method('deletePendingActivationsByDate')
            ->will($this->returnValue(2));

        $registry = $this->getMock(ManagerRegistry::class);
        $registry
            ->expects($this->any())
            ->method('getRepository')
            ->with('Account:User')
            ->will($this->returnValue($repository));

        return $registry;
    }
}
