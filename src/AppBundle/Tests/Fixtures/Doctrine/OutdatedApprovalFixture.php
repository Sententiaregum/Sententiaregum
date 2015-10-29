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

use AppBundle\Model\User\PendingActivation;
use AppBundle\Model\User\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Test fixture that contains outdated approvals.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OutdatedApprovalFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 2; $i++) {
            $user       = User::create($i, '123456', sprintf('%s@sententiaregum.dev', $i));
            $activation = new PendingActivation();

            $activation->setActivationDate(new \DateTime('-3 hours'));
            $user->setPendingActivation($activation);
            $manager->persist($user);
        }

        $newUser = User::create('foo', '123456', 'foo@bar.de');
        $newUser->setState(User::STATE_APPROVED);
        $manager->persist($newUser);

        $manager->flush();
    }
}
