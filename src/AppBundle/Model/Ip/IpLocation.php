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

namespace AppBundle\Model\Ip;

/**
 * Ip location class that contains all necessary data of the location of a ip.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class IpLocation
{
    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $city;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * Constructor.
     *
     * @param string $ip
     * @param string $country
     * @param string $region
     * @param string $city
     * @param float  $latitude
     * @param float  $longitude
     */
    public function __construct($ip, $country, $region, $city, $latitude, $longitude)
    {
        $this->ip        = (string) $ip;
        $this->country   = (string) $country;
        $this->region    = (string) $region;
        $this->city      = (string) $city;
        $this->latitude  = (float) $latitude;
        $this->longitude = (float) $longitude;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get region.
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get latitude.
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Get longitude.
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
