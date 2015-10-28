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
