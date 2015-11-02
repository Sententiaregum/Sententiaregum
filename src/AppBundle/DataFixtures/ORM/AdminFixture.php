<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
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
