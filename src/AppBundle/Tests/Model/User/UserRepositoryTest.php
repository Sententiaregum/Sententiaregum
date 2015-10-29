<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Model\User;

use AppBundle\Test\KernelTestCase;
use AppBundle\Tests\Fixtures\Doctrine\OutdatedApprovalFixture;

class UserRepositoryTest extends KernelTestCase
{
    protected function setUp()
    {
        $this->loadDataFixtures([OutdatedApprovalFixture::class]);
    }

    public function testRemovePendingActivations()
    {
        $dateTime = new \DateTime('-2 hours');
        /** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrine */
        $doctrine = $this->getService('doctrine');

        $repository = $doctrine->getRepository('Account:User');
        $this->assertCount(3, $repository->findAll());
        $this->assertSame(2, $repository->deletePendingActivationsByDate($dateTime));
        $this->assertCount(1, $repository->findAll());
    }
}
