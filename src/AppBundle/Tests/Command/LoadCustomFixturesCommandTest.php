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
