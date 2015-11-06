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
use AppBundle\Model\User\User;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

/**
 * Custom data fixture that creates an admin user.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class AdminFixture implements ProductionFixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $passwordHasher = new PhpPasswordHasher();
        $roleRepository = $manager->getRepository('Account:Role');
        $userRole       = $roleRepository->findOneBy(['role' => 'ROLE_USER']);
        $adminRole      = $roleRepository->findOneBy(['role' => 'ROLE_ADMIN']);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($passwordHasher->generateHash('123456'));
        $admin->setEmail('admin@sententiaregum.dev');
        $admin->addRole($adminRole);
        $admin->addRole($userRole);
        $admin->setLastAction(new \DateTime());
        $admin->setState(User::STATE_APPROVED);

        $manager->persist($admin);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}