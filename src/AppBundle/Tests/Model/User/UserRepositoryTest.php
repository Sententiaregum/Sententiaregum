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

namespace AppBundle\Tests\Model\User;

use AppBundle\DataFixtures\ORM\AdminFixture;
use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\DataFixtures\ORM\UserFixture;
use AppBundle\Test\KernelTestCase;
use AppBundle\Tests\Fixtures\Doctrine\OutdatedApprovalFixture;

class UserRepositoryTest extends KernelTestCase
{
    public function testRemovePendingActivations()
    {
        $this->loadDataFixtures([OutdatedApprovalFixture::class]);

        $dateTime = new \DateTime('-2 hours');
        /** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrine */
        $doctrine = $this->getService('doctrine');

        $repository = $doctrine->getRepository('Account:User');
        $this->assertCount(3, $repository->findAll());
        $this->assertSame(2, $repository->deletePendingActivationsByDate($dateTime));
        $this->assertCount(1, $repository->findAll());
    }

    public function testGetFollowingIds()
    {
        $this->loadDataFixtures([RoleFixture::class, AdminFixture::class, UserFixture::class]);

        /** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrine */
        $doctrine = $this->getService('doctrine');

        $repository = $doctrine->getRepository('Account:User');
        $ids        = $repository->getFollowingIdsByUser($repository->findOneBy(['username' => 'Ma27']));

        $this->assertCount(2, $ids);
    }
}
