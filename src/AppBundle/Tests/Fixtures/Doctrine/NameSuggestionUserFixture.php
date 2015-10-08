<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
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
