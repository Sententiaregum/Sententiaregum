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

namespace AppBundle\Tests\Unit\Model\User;

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
