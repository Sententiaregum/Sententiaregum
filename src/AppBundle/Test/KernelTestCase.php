<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;

/**
 * Enhanced kernel test case.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
abstract class KernelTestCase extends BaseKernelTestCase
{
    /**
     * Loads a set of datafixtures.
     *
     * @param array $fixtures
     */
    protected function loadDataFixtures(array $fixtures)
    {
        /** @var \AppBundle\Doctrine\ORM\ConfigurableFixturesLoader $service */
        $service = $this->getContainer()->get('app.doctrine.fixtures_loader');

        $service->applyFixtures($fixtures);
    }

    /**
     * Getter for the container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->getKernel()->getContainer();
    }

    /**
     * Getter for the kernel.
     *
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    protected function getKernel()
    {
        static::bootKernel();

        return self::$kernel;
    }

    /**
     * Getter for any service.
     *
     * @param string $id
     *
     * @return object
     */
    protected function getService($id)
    {
        return $this->getContainer()->get($id);
    }
}
