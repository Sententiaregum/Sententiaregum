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

namespace AppBundle\Service\Ip;

use AppBundle\Model\Ip\Provider\IpTracingServiceInterface;
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
    const PRIVATE_IPS = ['::1', '127.0.0.1'];

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
    public function getIpLocationData(string $ip, string $userLocale)
    {
        if (in_array($ip, self::PRIVATE_IPS, true)) {
            return;
        }

        try {
            $response = $this->client->get(
                sprintf('/json/%s', $ip),
                ['headers' => ['Accept-Language' => $userLocale ?: 'en']]
            );

            return $this->hydrateIpObject($ip, $this->decodePsr7Response($response->getBody()));
        } catch (ClientException $e) {
            if (!$this->isValidIp($ip)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid ip address "%s" given!',
                    $ip
                ));
            }
        }
    }

    /**
     * Hydrates the ip object with the given data.
     *
     * @param string  $ip
     * @param mixed[] $data
     *
     * @return IpLocation
     */
    private function hydrateIpObject(string $ip, array $data): IpLocation
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
    private function decodePsr7Response(StreamInterface $stream): array
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
    private function isValidIp(string $address): bool
    {
        return (bool) filter_var($address, FILTER_VALIDATE_IP);
    }
}
