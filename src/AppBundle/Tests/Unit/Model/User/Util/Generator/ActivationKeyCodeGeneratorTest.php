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

namespace AppBundle\Tests\Unit\Model\User\Util\Generator;

use AppBundle\Model\User\Util\ActivationKeyCode\ActivationKeyCodeGenerator;

class ActivationKeyCodeGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateKeyCode(): void
    {
        $generator = new ActivationKeyCodeGenerator();
        $string    = $generator->generate(30);

        $this->assertSame(30, strlen($string));
    }
}
