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
        for ($i = 0; $i < 2; ++$i) {
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
