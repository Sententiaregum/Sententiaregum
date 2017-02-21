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

use AppBundle\Model\User\User;
use AppBundle\Service\Doctrine\DataFixtures\ProductionFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

/**
 * Custom data fixture that creates an admin user.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class AdminFixture implements ProductionFixtureInterface, DependentFixtureInterface
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

        $admin = User::create('admin', '123456', 'admin@sententiaregum.dev', $passwordHasher);
        $admin->performStateTransition(User::STATE_APPROVED);
        $admin->addRole($adminRole);
        $admin->addRole($userRole);
        $admin->updateLastAction();

        $manager->persist($admin);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            RoleFixture::class,
        ];
    }
}
