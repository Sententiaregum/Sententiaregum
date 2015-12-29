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
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;
use Ramsey\Uuid\Uuid;

/**
 * Custom data fixture that creates an admin user.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class AdminFixture extends BaseFixture implements ProductionFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /* @var \Doctrine\ORM\EntityManager $manager */
        $this->checkEntityManager($manager);

        $passwordHasher = new PhpPasswordHasher();
        $roleRepository = $manager->getRepository('Account:Role');
        $userRole       = $roleRepository->findOneBy(['role' => 'ROLE_USER']);
        $adminRole      = $roleRepository->findOneBy(['role' => 'ROLE_ADMIN']);

        $admin = new User();
        $admin->setState(User::STATE_APPROVED);
        $admin->setUsername('admin');
        $admin->setPassword($passwordHasher->generateHash('123456'));
        $admin->setEmail('admin@sententiaregum.dev');
        $admin->addRole($adminRole);
        $admin->addRole($userRole);
        $admin->setLastAction(new \DateTime());

        $manager->persist($admin);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            RoleFixture::class,
        ];
    }
}
