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

namespace AppBundle\Tests\Model\User\Util;

use AppBundle\Model\User\Util\DateTimeComparison;

class DateTimeComparisonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string    $diff
     * @param \DateTime $dateTime
     * @param bool      $expected
     *
     * @dataProvider provideDateDiffComparison
     */
    public function testDateDiffComparison($diff, \DateTime $dateTime, $expected)
    {
        $service = new DateTimeComparison();
        $this->assertSame($service($diff, $dateTime), $expected);
    }

    public function provideDateDiffComparison()
    {
        return [
            [
                '-12 hours',
                new \DateTime('-6 hours'),
                true,
            ],
            [
                '-6 hours',
                new \DateTime('-12 hours'),
                false,
            ],
            [
                '-12 hours',
                new \DateTime('-12 hours'),
                false,
            ],
        ];
    }
}
