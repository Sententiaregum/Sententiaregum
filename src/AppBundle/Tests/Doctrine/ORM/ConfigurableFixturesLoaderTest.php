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

use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\Test\KernelTestCase;

class ConfigurableFixturesLoaderTest extends KernelTestCase
{
    public function testLoadFixtures()
    {
        /** @var \AppBundle\Doctrine\ORM\ConfigurableFixturesLoader $service */
        $service = $this->getService('app.doctrine.fixtures_loader');

        $service->loadFixtures([RoleFixture::class]);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $this->getService('doctrine.orm.default_entity_manager');
        $this->assertNotNull($entityManager->getRepository('Account:Role')->findOneBy(['role' => 'ROLE_USER']));
    }
}
