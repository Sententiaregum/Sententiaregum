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

use AppBundle\Model\User\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

/**
 * Fixture which is responsible for the user entity.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UserFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $passwordHasher = new PhpPasswordHasher();

        $roleRepository = $manager->getRepository('User:Role');
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

        $user1 = new User();
        $user1->setUsername('Ma27');
        $user1->setPassword($passwordHasher->generateHash('72aM'));
        $user1->setEmail('Ma27@sententiaregum.dev');
        $user1->addRole($userRole);
        $user1->setLastAction(new \DateTime());
        $user1->setState(User::STATE_APPROVED);
        $user1->setLocale('de');

        $user2 = new User();
        $user2->setUsername('benbieler');
        $user2->setPassword($passwordHasher->generateHash('releibneb'));
        $user2->setEmail('benbieler@sententiaregum.dev');
        $user2->addRole($userRole);
        $user2->setLastAction(new \DateTime());
        $user2->setState(User::STATE_APPROVED);

        $locked = new User();
        $locked->setUsername('anonymus');
        $locked->setPassword($passwordHasher->generateHash('sumynona'));
        $locked->setEmail('anonymus@example.org');
        $locked->addRole($userRole);
        $locked->lock();
        $locked->setLastAction(new \DateTime());
        $locked->setState(User::STATE_APPROVED);

        $user2->addFollowing($user1);
        $user1->addFollowing($user2);
        $user1->addFollowing($admin);

        foreach ([$admin, $user1, $user2, $locked] as $userModel) {
            $manager->persist($userModel);
        }

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
