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

namespace AppBundle\Tests\Unit\EventListener;

use AppBundle\EventListener\NotFoundResponseListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class NotFoundResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param Request    $request
     * @param \Exception $exception
     *
     * @dataProvider provideReplacementData
     */
    public function testReplaceResponse(Request $request, \Exception $exception, string $expected)
    {
        $listener = new NotFoundResponseListener();
        $event    = new GetResponseForExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $listener->onKernelException($event);
        $response = $event->getResponse();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame($expected, $response->getTargetUrl());
    }

    /**
     * @return array
     */
    public function provideReplacementData(): array
    {
        return [
            [
                Request::create('/invalid_url'),
                new NotFoundHttpException(),
                '/#/invalid_url',
            ],
            [
                Request::create('/invalid/url?foo=bar#blah'),
                new NotFoundHttpException(),
                '/#/invalid/url?foo=bar',
            ],
            [ // specification needed for this case as API should be a separated prefix
                Request::create('/apifoo'),
                new NotFoundHttpException(),
                '/#/apifoo',
            ],
            [
                Request::create('/blah#foo'),
                new NotFoundHttpException(),
                '/#/blah',
            ],
            [
                Request::create('/blah?data'),
                new NotFoundHttpException(),
                '/#/blah?data',
            ],
            [
                Request::create('/blah?data[]=foo&data[]=more_data'),
                new NotFoundHttpException(),
                '/#/blah?data[]=foo&data[]=more_data',
            ],
        ];
    }

    /**
     * @param \Exception $exception
     * @param Request    $request
     *
     * @dataProvider provideAbortData
     */
    public function testAbortOnInvalidConditions(\Exception $exception, Request $request)
    {
        $listener = new NotFoundResponseListener();
        $event    = new GetResponseForExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    /**
     * @return array
     */
    public function provideAbortData(): array
    {
        return [
            [
                new AccessDeniedHttpException(),
                Request::create('/invalid_url'),
            ],
            [
                new NotFoundHttpException(),
                Request::create('/api/invalid_url.json'),
            ],
        ];
    }
}
