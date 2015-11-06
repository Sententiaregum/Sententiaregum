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
