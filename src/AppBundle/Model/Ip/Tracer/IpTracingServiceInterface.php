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

namespace AppBundle\Model\Ip\Tracer;

/**
 * Interface which attempts to trace an ip and returns an object containing all the necessary data.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface IpTracingServiceInterface
{
    /**
     * Gets the ip location object.
     *
     * @param string $ip
     * @param string $userLocale
     *
     * @return \AppBundle\Model\Ip\Value\IpLocation|null
     */
    public function getIpLocationData($ip, $userLocale);
}
