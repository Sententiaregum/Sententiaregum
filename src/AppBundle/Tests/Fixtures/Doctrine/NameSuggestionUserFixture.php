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

use AppBundle\Model\User\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Test fixture for the name suggestor.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class NameSuggestionUserFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist(User::create('Ma27', '123456', 'Ma27@sententiaregum.dev'));
        $manager->persist(User::create('Ma27'.(string) date('Y'), '123456', 'foo@example.org'));

        $manager->flush();
    }
}
