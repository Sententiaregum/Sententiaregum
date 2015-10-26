<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Behat\Comparator;

use AppBundle\Behat\Comparator\BooleanComparator;

class BooleanComparatorTest extends \PHPUnit_Framework_TestCase
{
    public function testAccepts()
    {
        $comparator = new BooleanComparator();
        $this->assertTrue($comparator->accepts(true, 'blah'));
        $this->assertFalse($comparator->accepts('blah', 'blah'));
    }

    /**
     * @expectedException \SebastianBergmann\Comparator\ComparisonFailure
     */
    public function testCompareToTrue()
    {
        $comparator = new BooleanComparator();
        $comparator->assertEquals(true, false);
    }

    /**
     * @expectedException \SebastianBergmann\Comparator\ComparisonFailure
     */
    public function testCompareToFalse()
    {
        $comparator = new BooleanComparator();
        $comparator->assertEquals(false, true);
    }
}
