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
        $userRole       = $manager->getRepository('Account:Role')->findOneBy(['role' => 'ROLE_USER']);

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
        $user1->addFollowing($manager->getRepository('Account:User')->findOneBy(['username' => 'admin']));

        foreach ([$user1, $user2, $locked] as $userModel) {
            $manager->persist($userModel);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
