<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Behat\Comparator;

use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Boolean comparator that can be used with the comparator lib of Sebastian Bergmann.
 * This lib is in use with Behat, but a boolean comparator is still missing.
 */
class BooleanComparator extends Comparator
{
    /**
     * {@inheritdoc}
     */
    public function accepts($expected, $actual)
    {
        return is_bool($expected);
    }

    /**
     * {@inheritdoc}
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        $expr = $expected ? $actual : !$actual;

        if (!$expr) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                $this->exporter->export($expected),
                $this->exporter->export($actual)
            );
        }
    }
}
