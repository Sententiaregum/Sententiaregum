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

namespace AppBundle\Model\Ip\Provider;

use AppBundle\Model\Ip\Value\IpLocation;

/**
 * Interface which attempts to trace an ip and returns an object containing all the necessary data.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface IpTracingServiceInterface
{
    /**
     * Gets the ip location object.
     *
     * @param string $ip
     * @param string $userLocale
     *
     * @return \AppBundle\Model\Ip\Value\IpLocation
     */
    public function getIpLocationData(string $ip, string $userLocale): IpLocation;
}
