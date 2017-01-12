<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Tests\Unit\Model\User;

use AppBundle\Model\User\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization(): void
    {
        $role       = new Role('ROLE_USER');
        $serialized = serialize($role);

        $this->assertNotEmpty($serialized);
        $newRole = unserialize($serialized);

        $this->assertSame('ROLE_USER', $newRole->getRole());
    }
}
