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

use AppBundle\Command\LoadCustomFixturesCommand;
use AppBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class LoadCustomFixturesCommandTest extends KernelTestCase
{
    public function testApplyProductionFixtures()
    {
        $tester = $this->getTester();

        $tester->execute(['bundles' => ['AppBundle'], '--no-interaction'], ['interactive' => false]);
        $display = $tester->getDisplay();

        $this->assertNotFalse(strpos($display, 'purging database'));
        $this->assertNotFalse(strpos($display, 'AppBundle\DataFixtures\ORM\RoleFixture'));
        $this->assertNotFalse(strpos($display, 'AppBundle\DataFixtures\ORM\AdminFixture'));

        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em = $this->getService('doctrine.orm.default_entity_manager');

        $roleRepository = $em->getRepository('Account:Role');
        $userRepository = $em->getRepository('Account:User');

        $this->assertNotNull($roleRepository->findOneBy(['role' => 'ROLE_USER']));
        $this->assertNotNull($roleRepository->findOneBy(['role' => 'ROLE_ADMIN']));
        $this->assertNotNull($userRepository->findOneBy(['username' => 'admin']));
    }

    /**
     * Creates the command tester.
     *
     * @return CommandTester
     */
    private function getTester()
    {
        $application = new Application($this->getKernel());
        $application->add(new LoadCustomFixturesCommand());

        return new CommandTester($application->find('sententiaregum:fixtures:production'));
    }
}
