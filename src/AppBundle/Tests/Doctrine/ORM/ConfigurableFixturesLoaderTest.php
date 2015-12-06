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

namespace AppBundle\Tests\Doctrine\ORM;

use AppBundle\DataFixtures\ORM\AdminFixture;
use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\FixtureInterface;

class ConfigurableFixturesLoaderTest extends KernelTestCase
{
    public function testLoadFixtures()
    {
        /** @var \AppBundle\Doctrine\ORM\ConfigurableFixturesLoader $service */
        $service = $this->getService('app.doctrine.fixtures_loader');

        $service->applyFixtures([RoleFixture::class]);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $this->getService('doctrine.orm.default_entity_manager');
        $this->assertNotNull($entityManager->getRepository('Account:Role')->findOneBy(['role' => 'ROLE_USER']));
    }

    public function testLogFixtureLoad()
    {
        /** @var \AppBundle\Doctrine\ORM\ConfigurableFixturesLoader $service */
        $service = $this->getService('app.doctrine.fixtures_loader');
        $called  = false;

        $service->applyFixtures([RoleFixture::class], function () use (&$called) {
            $called = true;
        });

        $this->assertTrue($called, 'Missing logger call!');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Fixture class "Invalid Class Name" does not exist!
     */
    public function testInvalidFixtureClass()
    {
        /** @var \AppBundle\Doctrine\ORM\ConfigurableFixturesLoader $service */
        $service = $this->getService('app.doctrine.fixtures_loader');

        $service->applyFixtures(['Invalid Class Name']);
    }

    public function testGetProductionFixturesByDirectory()
    {
        /** @var \AppBundle\Doctrine\ORM\ConfigurableFixturesLoader $service */
        $service = $this->getService('app.doctrine.fixtures_loader');

        $fixtureInstances = $service->loadProductionFixturesFromDirectory(__DIR__.'/../../../DataFixtures/ORM');

        $classList = array_map(
            function (FixtureInterface $fixture) {
                return get_class($fixture);
            },
            $fixtureInstances
        );

        $this->assertContains(AdminFixture::class, $classList);
        $this->assertContains(RoleFixture::class, $classList);
    }
}
