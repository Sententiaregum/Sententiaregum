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

namespace AppBundle\Tests\Fixtures\Doctrine;

use AppBundle\DataFixtures\ORM\BaseFixture;
use AppBundle\Model\User\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Id\UuidGenerator;

/**
 * Test fixture for the name suggestor.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class NameSuggestionUserFixture extends BaseFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /* @var \Doctrine\ORM\EntityManager $manager */
        $this->checkEntityManager($manager);

        $user1 = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev');
        $user2 = User::create('Ma27'.(string) date('Y'), '123456', 'foo@example.org');

        /** @var User $user */
        foreach ([$user1, $user2] as $user) {
            $user->setId((new UuidGenerator())->generate($manager, $user));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
