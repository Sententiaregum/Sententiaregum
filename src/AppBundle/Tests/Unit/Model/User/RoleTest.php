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

use AppBundle\Model\User\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $role       = new Role('ROLE_USER');
        $serialized = serialize($role);

        $this->assertNotEmpty($serialized);
        $newRole = unserialize($serialized);

        $this->assertSame('ROLE_USER', $newRole->getRole());
    }
}
