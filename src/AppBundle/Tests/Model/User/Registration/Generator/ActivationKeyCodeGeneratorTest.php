<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Model\User\Registration\Generator;

use AppBundle\Model\User\Registration\Generator\ActivationKeyCodeGenerator;

class ActivationKeyCodeGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateKeyCode()
    {
        $generator = new ActivationKeyCodeGenerator();
        $string    = $generator->generate(30);

        $this->assertSame(30, strlen($string));
    }
}
