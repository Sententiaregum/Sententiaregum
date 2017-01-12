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

namespace AppBundle\Model\User\Util\Date;

/**
 * Simple domain service which utilizes the datetime comparison behavior.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
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
    public function __invoke(string $maximumRange, \DateTime $attemptDate): bool
    {
        return (new \DateTime($maximumRange))->getTimestamp() <= $attemptDate->getTimestamp();
    }
}
