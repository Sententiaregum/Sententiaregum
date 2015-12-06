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

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

/**
 * Abstract fixture class.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
abstract class BaseFixture implements FixtureInterface
{
    /**
     * Checks the model manager.
     *
     * @param ObjectManager $om
     *
     * @throws \RuntimeException If the manager is invalid
     */
    public function checkEntityManager(ObjectManager $om)
    {
        if (!$om instanceof EntityManager) {
            throw new \RuntimeException('This system requires an entity manager!');
        }
    }
}
