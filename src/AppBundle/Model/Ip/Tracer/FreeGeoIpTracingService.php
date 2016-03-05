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

use AppBundle\Model\Ip\Value\IpLocation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\StreamInterface;

/**
 * Service which attempts to locate ip addresses.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class FreeGeoIpTracingService implements IpTracingServiceInterface
{
    private static $PRIVATE_IPS = ['::1', '127.0.0.1'];

    /**
     * @var Client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If the ip is invalid.
     */
    public function getIpLocationData($ip, $userLocale)
    {
        if (in_array($ip, self::$PRIVATE_IPS, true)) {
            return;
        }

        try {
            $response = $this->client->get(
                sprintf('/json/%s', $ip),
                ['headers' => ['Accept-Language' => $userLocale ?: 'en']]
            );

            $result = $this->hydrateIpObject($ip, $this->decodePsr7Response($response->getBody()));
        } catch (ClientException $e) {
            if (!$this->isValidIp($ip)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid ip address "%s" given!',
                    $ip
                ));
            }

            $result = null;
        }

        return $result;
    }

    /**
     * Hydrates the ip object with the given data.
     *
     * @param string  $ip
     * @param mixed[] $data
     *
     * @return IpLocation
     */
    private function hydrateIpObject($ip, array $data)
    {
        return new IpLocation(
            $ip,
            $data['country_name'],
            $data['region_name'],
            $data['city'],
            $data['latitude'],
            $data['longitude']
        );
    }

    /**
     * Decodes a psr7 json stream.
     *
     * @param StreamInterface $stream
     *
     * @throws \RuntimeException If the decode fails.
     *
     * @return mixed[]
     */
    private function decodePsr7Response(StreamInterface $stream)
    {
        $body = (string) $stream;
        if (!($decoded = json_decode($body, true)) && JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(sprintf(
                'Unable to decode response body ("%s") due to the following error "%s"!',
                $body,
                json_last_error_msg()
            ));
        }

        return $decoded;
    }

    /**
     * Checks whether the given ip is valid or not.
     *
     * @param string $address
     *
     * @return bool
     */
    private function isValidIp($address)
    {
        return filter_var($address, FILTER_VALIDATE_IP);
    }
}
