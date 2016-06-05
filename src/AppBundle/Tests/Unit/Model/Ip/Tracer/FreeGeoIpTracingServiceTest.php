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

namespace AppBundle\Tests\Model\Ip\Tracer;

use AppBundle\Model\Ip\Tracer\FreeGeoIpTracingService;
use AppBundle\Model\Ip\Value\IpLocation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class FreeGeoIpTracingServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getIps
     */
    public function testGetGetLocationData($ip)
    {
        $stream = $this->getMock(StreamInterface::class);
        $stream
            ->expects($this->any())
            ->method('__toString')
            ->willReturn(json_encode(
                [
                    'country_name' => 'Germany',
                    'region_name'  => 'Bavaria',
                    'city'         => 'Munich',
                    'latitude'     => 48.813,
                    'longitude'    => 11.3437,
                ]
            ));

        $response = $this->getMock(ResponseInterface::class);
        $response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->getMockWithoutInvokingTheOriginalConstructor(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with('get', ['/json/'.$ip, ['headers' => ['Accept-Language' => 'en']]])
            ->willReturn($response);

        $service = new FreeGeoIpTracingService($client);
        $result  = $service->getIpLocationData($ip, 'en');

        $this->assertInstanceOf(IpLocation::class, $result);
        $this->assertSame('Germany', $result->getCountry());
        $this->assertSame($ip, $result->getIp());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid ip address "foo" given!
     */
    public function testInvalidIp()
    {
        $client = $this->getMockWithoutInvokingTheOriginalConstructor(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with('get', ['/json/foo', ['headers' => ['Accept-Language' => 'en']]])
            ->will($this->returnCallback(function () {
                throw new ClientException('404 file not found', $this->getMockWithoutInvokingTheOriginalConstructor(Request::class));
            }));

        $service = new FreeGeoIpTracingService($client);
        $service->getIpLocationData('foo', 'en');
    }

    public function testCannotTrack()
    {
        $client = $this->getMockWithoutInvokingTheOriginalConstructor(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with('get', ['/json/192.168.56.112', ['headers' => ['Accept-Language' => 'en']]])
            ->will($this->returnCallback(function () {
                throw new ClientException('404 file not found', $this->getMockWithoutInvokingTheOriginalConstructor(Request::class));
            }));

        $service = new FreeGeoIpTracingService($client);
        $result  = $service->getIpLocationData('192.168.56.112', 'en');

        $this->assertNull($result);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Unable to decode response body \("\{ip"\:"192\.168\.56\.112"\}"\) due to the following error \".*\"!$/
     */
    public function testBrokenResponse()
    {
        $stream = $this->getMock(StreamInterface::class);
        $stream
            ->expects($this->any())
            ->method('__toString')
            ->willReturn('{ip":"192.168.56.112"}');

        $response = $this->getMock(ResponseInterface::class);
        $response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->getMockWithoutInvokingTheOriginalConstructor(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with('get', ['/json/192.168.56.112', ['headers' => ['Accept-Language' => 'en']]])
            ->willReturn($response);

        $service = new FreeGeoIpTracingService($client);
        $service->getIpLocationData('192.168.56.112', 'en');
    }

    /**
     * @dataProvider getLocalIps
     */
    public function testLocalIp($ip)
    {
        $client = $this->getMockWithoutInvokingTheOriginalConstructor(Client::class);

        $service = new FreeGeoIpTracingService($client);
        $this->assertNull($service->getIpLocationData($ip, 'en'));
    }

    /**
     * Data provider to test ipv4 and ipv6.
     *
     * @return string[]
     */
    public function getIps()
    {
        return [
            ['192.168.56.112'],
            ['FE80:0000:0000:0000:0202:B3FF:FE1E:8329'],
        ];
    }

    /**
     * Provider for local ips.
     *
     * @return string[]
     */
    public function getLocalIps()
    {
        return [
            ['127.0.0.1'],
            ['::1'],
        ];
    }
}
