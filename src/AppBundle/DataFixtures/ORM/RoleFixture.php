<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Model\User\Role;
use AppBundle\Service\Doctrine\DataFixtures\ProductionFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixture class that creates the basic role entities.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class RoleFixture implements ProductionFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $userRole  = new Role('ROLE_USER');
        $adminRole = new Role('ROLE_ADMIN');

        $manager->persist($userRole);
        $manager->persist($adminRole);

        $manager->flush();
    }
}
