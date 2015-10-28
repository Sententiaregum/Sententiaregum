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

use AppBundle\Model\User\PendingActivation;

class PendingActivationTest extends \PHPUnit_Framework_TestCase
{
    public function testIsOutdatedRegistration()
    {
        $model = new PendingActivation();
        $model->setActivationDate(new \DateTime('-3 hours'));

        $this->assertTrue($model->isActivationExpired());
    }

    public function testIsNonExpiredRegistration()
    {
        $model = new PendingActivation();
        $model->setActivationDate(new \DateTime());

        $this->assertFalse($model->isActivationExpired());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Missing activation date!
     */
    public function testNoActivationDateIsGiven()
    {
        $model = new PendingActivation();
        $model->isActivationExpired();
    }
}
