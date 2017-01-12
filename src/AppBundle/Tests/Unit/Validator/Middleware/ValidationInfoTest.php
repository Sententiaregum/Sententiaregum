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

namespace AppBundle\Tests\Unit\Validator\Middleware;

use AppBundle\Validator\Middleware\ValidationInfo;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetViolationList(): void
    {
        $info       = new ValidationInfo();
        $violations = new ConstraintViolationList([
            new ConstraintViolation('Error!', '<error template>', [], 'root', 'root.property', 'foobar'),
        ]);

        $info->violationList = $violations;

        $this->assertFalse($info->isValid());
        $this->assertSame($violations, $info->violationList);
    }

    public function testEmptyViolationList(): void
    {
        $info = new ValidationInfo();

        $info->violationList = new ConstraintViolationList();

        $this->assertTrue($info->isValid());
    }

    public function testNoViolationsGiven(): void
    {
        $info = new ValidationInfo();

        $this->assertTrue($info->isValid());
    }

    public function testExtraData(): void
    {
        $info = new ValidationInfo();

        $info->extra['key'] = 'data';

        $this->assertSame($info->getExtraValue('key'), 'data');
    }

    public function testOptionalExtraDataMissing(): void
    {
        $info = new ValidationInfo();

        $this->assertNull($info->getExtraValue('optional_data', true));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Missing property "required_data" in extra data!
     */
    public function testRequiredExtraDataMissing(): void
    {
        $info = new ValidationInfo();

        $info->getExtraValue('required_data');
    }
}
