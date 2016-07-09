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

declare(strict_types=1);

namespace AppBundle\Tests\Unit\Validator\Middleware;

use AppBundle\Validator\Middleware\ValidationInfo;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetViolationList()
    {
        $info       = new ValidationInfo();
        $violations = new ConstraintViolationList([
            new ConstraintViolation('Error!', '<error template>', [], 'root', 'root.property', 'foobar')
        ]);

        $info->violationList = $violations;

        $this->assertFalse($info->isValid());
        $this->assertSame($violations, $info->violationList);
    }

    public function testEmptyViolationList()
    {
        $info = new ValidationInfo();

        $info->violationList = new ConstraintViolationList();

        $this->assertTrue($info->isValid());
    }

    public function testNoViolationsGiven()
    {
        $info = new ValidationInfo();

        $this->assertTrue($info->isValid());
    }

    public function testExtraData()
    {
        $info = new ValidationInfo();

        $info->extra['key'] = 'data';

        $this->assertSame($info->getExtraValue('key'), 'data');
    }

    public function testOptionalExtraDataMissing()
    {
        $info = new ValidationInfo();

        $this->assertNull($info->getExtraValue('optional_data', true));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Missing property "required_data" in extra data!
     */
    public function testRequiredExtraDataMissing()
    {
        $info = new ValidationInfo();

        $info->getExtraValue('required_data');
    }
}
