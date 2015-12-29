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

use AppBundle\Model\User\AuthenticationAttempt;

class AuthenticationAttemptTest extends \PHPUnit_Framework_TestCase
{
    public function testIncreaseCount()
    {
        $attempt = new AuthenticationAttempt();
        $this->assertSame(0, $attempt->getAttemptCount());

        $attempt->increaseAttemptCount()->increaseAttemptCount();

        $this->assertSame(2, $attempt->getAttemptCount());
    }
}
