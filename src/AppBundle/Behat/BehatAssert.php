<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Behat;

use AppBundle\Behat\Comparator\BooleanComparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory;
use SebastianBergmann\Exporter\Exporter;

/**
 * Assert class for behat contexts.
 */
abstract class BehatAssert
{
    /**
     * @var Factory
     */
    protected $comparisonFactory;

    /**
     * Gets the comparison factory.
     *
     * @return Factory
     */
    protected function getComparisonFactory()
    {
        if (null !== $this->comparisonFactory) {
            return $this->comparisonFactory;
        }

        $factory = new Factory();
        $factory->register(new BooleanComparator());

        return $this->comparisonFactory = $factory;
    }

    /**
     * Asserts that two elements are equal.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    protected function assertEquals($expected, $actual, $message = null)
    {
        $comparator = $this->getComparisonFactory()->getComparatorFor($expected, $actual);

        try {
            $comparator->assertEquals($expected, $actual);
        } catch (ComparisonFailure $ex) {
            if (null !== $message) {
                $ex = new ComparisonFailure(
                    $expected,
                    $actual,
                    $ex->getExpectedAsString(),
                    $ex->getActualAsString(),
                    false,
                    $message
                );
            }

            throw $ex;
        }
    }

    /**
     * Asserts whether something is true.
     *
     * @param bool   $actual
     * @param string $message
     */
    protected function assertTrue($actual, $message = null)
    {
        $this->assertEquals(true, $actual, $message);
    }

    /**
     * Asserts whether something is false.
     *
     * @param bool   $actual
     * @param string $message
     */
    protected function assertFalse($actual, $message = null)
    {
        $this->assertEquals(false, $actual, $message);
    }

    /**
     * Asserts length of an array.
     *
     * @param int    $count
     * @param array  $array
     * @param string $message
     */
    protected function assertCount($count, array $array, $message = null)
    {
        $this->assertEquals(count($array), (int) $count, $message);
    }

    /**
     * Asserts that two values are not equal.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    protected function assertNotEquals($expected, $actual, $message = null)
    {
        try {
            $this->assertEquals($expected, $actual, $message);
        } catch (ComparisonFailure $ex) {
            return;
        }

        $exporter = new Exporter();
        throw new ComparisonFailure($expected, $actual, $exporter->export($expected), $exporter->export($actual), $message);
    }
}
