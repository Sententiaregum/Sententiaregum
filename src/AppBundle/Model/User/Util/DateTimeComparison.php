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

namespace AppBundle\Model\User\Util;

/**
 * Simple domain service which utilizes the datetime comparison behavior.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class DateTimeComparison
{
    /**
     * Executes the datetime comparison.
     *
     * @param string    $maximumRange
     * @param \DateTime $attemptDate
     *
     * @return bool
     */
    public function __invoke($maximumRange, \DateTime $attemptDate)
    {
        return (new \DateTime($maximumRange))->getTimestamp() <= $attemptDate->getTimestamp();
    }
}
