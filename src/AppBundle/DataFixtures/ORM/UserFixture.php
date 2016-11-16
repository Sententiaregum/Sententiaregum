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

declare(strict_types=1);

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Model\User\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

/**
 * Fixture which is responsible for the user entity.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UserFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $passwordHasher = new PhpPasswordHasher();
        $userRole       = $manager->getRepository('Account:Role')->findOneBy(['role' => 'ROLE_USER']);

        $user1 = User::create('Ma27', '72aM', 'Ma27@sententiaregum.dev', $passwordHasher);
        $user1->performStateTransition(User::STATE_APPROVED);
        $user1->addRole($userRole);
        $user1->updateLastAction();
        $user1->modifyUserLocale('de');

        $user2 = User::create('benbieler', 'releibneb', 'benbieler@sententiaregum.dev', $passwordHasher);
        $user2->performStateTransition(User::STATE_APPROVED);
        $user2->addRole($userRole);
        $user2->updateLastAction();

        $locked = User::create('anonymus', 'sumynona', 'anonymus@example.org', $passwordHasher);
        $locked->performStateTransition(User::STATE_APPROVED);
        $locked->addRole($userRole);
        $locked->performStateTransition(User::STATE_LOCKED);
        $locked->updateLastAction();

        $user2->addFollowing($user1);
        $user1->addFollowing($user2);
        $user1->addFollowing($manager->getRepository('Account:User')->findOneBy(['username' => 'admin']));

        /** @var User $userModel */
        foreach ([$user1, $user2, $locked] as $userModel) {
            $manager->persist($userModel);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            AdminFixture::class,
            RoleFixture::class,
        ];
    }
}
