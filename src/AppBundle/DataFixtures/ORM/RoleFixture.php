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

use AppBundle\Doctrine\ORM\ProductionFixtureInterface;
use AppBundle\Model\User\Role;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Id\UuidGenerator;

/**
 * Fixture class that creates the basic role entities.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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

        $userRole->setId((new UuidGenerator())->generate($manager, $userRole));
        $adminRole->setId((new UuidGenerator())->generate($manager, $adminRole));

        $manager->persist($userRole);
        $manager->persist($adminRole);

        $manager->flush();
    }
}
