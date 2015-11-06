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
