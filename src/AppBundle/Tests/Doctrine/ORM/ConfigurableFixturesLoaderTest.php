<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Doctrine\ORM;

use AppBundle\DataFixtures\ORM\AdminFixture;
use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\Test\KernelTestCase;

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

        $fixture = array_shift($fixtureInstances);
        $this->assertInstanceOf(AdminFixture::class, $fixture);

        $fixture = array_shift($fixtureInstances);
        $this->assertInstanceOf(RoleFixture::class, $fixture);
    }
}
