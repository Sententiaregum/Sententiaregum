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

namespace AppBundle\Model\Ip\Value;

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
    public function __construct(string $ip, string $country, string $region, string $city, float $latitude, float $longitude)
    {
        $this->ip        = $ip;
        $this->country   = $country;
        $this->region    = $region;
        $this->city      = $city;
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Get region.
     *
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Get latitude.
     *
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * Get longitude.
     *
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
