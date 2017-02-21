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

namespace AppBundle\Tests\Unit\EventListener;

use AppBundle\EventListener\LocaleCookieMatchingListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LocaleCookieMatchingListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchLocale(): void
    {
        $request = Request::create('/');
        $request->cookies->set('language', 'de');

        $event    = new GetResponseEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MASTER_REQUEST);
        $listener = new LocaleCookieMatchingListener('en');

        $listener->onKernelRequest($event);

        $this->assertSame('de', $request->getLocale());
        $this->assertSame('de', $request->attributes->get('_locale'));
    }

    public function testSubRequest(): void
    {
        $request = Request::create('/');
        $request->cookies->set('language', 'de');
        $request->setLocale('en');
        $request->attributes->set('_locale', 'en');

        $event    = new GetResponseEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::SUB_REQUEST);
        $listener = new LocaleCookieMatchingListener('en');

        $listener->onKernelRequest($event);

        $this->assertSame('en', $request->getLocale());
        $this->assertSame('en', $request->attributes->get('_locale'));
    }
}
