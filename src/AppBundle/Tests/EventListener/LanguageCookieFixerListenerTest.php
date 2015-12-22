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

namespace AppBundle\Tests\EventListener;

use AppBundle\EventListener\LanguageCookieFixerListener;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class LanguageCookieFixerListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getRequests
     */
    public function testWrongRequest(Request $request, Response $response)
    {
        $event = new FilterResponseEvent($this->getKernel(), $request, KernelInterface::MASTER_REQUEST, $response);
        $repo  = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);

        $listener = new LanguageCookieFixerListener($repo);
        $listener->onResponseFilter($event);

        $this->assertCount(0, $response->headers->getCookies());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^Cannot extract api key from response due to the following error: ".*"\!$/
     */
    public function testFailedApiKeyExtraction()
    {
        $request1 = Request::create('/');
        $request1->attributes->set('_route', 'ma27_api_key_authentication.request');

        $event = new FilterResponseEvent($this->getKernel(), $request1, KernelInterface::MASTER_REQUEST, Response::create('[', 200));
        $repo  = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);

        $listener = new LanguageCookieFixerListener($repo);
        $listener->onResponseFilter($event);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot extract api key from response!
     */
    public function testMissingKey()
    {
        $request1 = Request::create('/');
        $request1->attributes->set('_route', 'ma27_api_key_authentication.request');

        $event = new FilterResponseEvent($this->getKernel(), $request1, KernelInterface::MASTER_REQUEST, Response::create('[]', 200));
        $repo  = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);

        $listener = new LanguageCookieFixerListener($repo);
        $listener->onResponseFilter($event);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot find user for api key "12345"!
     */
    public function testInvalidApiKey()
    {
        $request1 = Request::create('/');
        $request1->attributes->set('_route', 'ma27_api_key_authentication.request');

        $event = new FilterResponseEvent($this->getKernel(), $request1, KernelInterface::MASTER_REQUEST, Response::create('{"apiKey":"12345"}'));
        $repo  = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);

        $listener = new LanguageCookieFixerListener($repo);
        $listener->onResponseFilter($event);
    }

    public function testValidCookie()
    {
        $request1 = Request::create('/', 'GET', [], ['language' => 'en']);
        $request1->attributes->set('_route', 'ma27_api_key_authentication.request');

        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev');

        $event = new FilterResponseEvent($this->getKernel(), $request1, KernelInterface::MASTER_REQUEST, Response::create('{"apiKey":"12345"}'));
        $repo  = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);
        $repo
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($user);

        $listener = new LanguageCookieFixerListener($repo);
        $listener->onResponseFilter($event);

        $this->assertCount(0, $event->getResponse()->headers->getCookies());
    }

    public function testNewCookie()
    {
        $request1 = Request::create('/', 'GET', [], ['language' => 'en']);
        $request1->attributes->set('_route', 'ma27_api_key_authentication.request');

        $user = User::create('Ma27', '123456', 'Ma27@sententiaregum.dev');
        $user->setLocale('de');

        $event = new FilterResponseEvent($this->getKernel(), $request1, KernelInterface::MASTER_REQUEST, Response::create('{"apiKey":"12345"}'));
        $repo  = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);
        $repo
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($user);

        $listener = new LanguageCookieFixerListener($repo);
        $listener->onResponseFilter($event);

        $cookie = $event->getResponse()->headers->getCookies()[0];
        $this->assertSame($cookie->getName(), 'language');
        $this->assertSame($cookie->getValue(), 'de');
    }

    public function getRequests()
    {
        $request1 = Request::create('/');
        $request1->attributes->set('_route', 'ma27_api_key_authentication.request');

        return [
            [
                $request1,
                Response::create('', 401),
            ],
            [
                Request::create('/'),
                Response::create('', 200),
            ],
        ];
    }

    /**
     * @return KernelInterface
     */
    private function getKernel()
    {
        return $this->getMock(KernelInterface::class);
    }
}
