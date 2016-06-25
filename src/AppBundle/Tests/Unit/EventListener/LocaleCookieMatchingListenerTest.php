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

use AppBundle\EventListener\LocaleCookieMatchingListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LocaleCookieMatchingListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchLocale()
    {
        $request = Request::create('/');
        $request->cookies->set('language', 'de');

        $event    = new GetResponseEvent($this->getMock(HttpKernelInterface::class), $request, HttpKernelInterface::MASTER_REQUEST);
        $listener = new LocaleCookieMatchingListener('en');

        $listener->onKernelRequest($event);

        $this->assertSame('de', $request->getLocale());
        $this->assertSame('de', $request->attributes->get('_locale'));
    }

    public function testSubRequest()
    {
        $request = Request::create('/');
        $request->cookies->set('language', 'de');
        $request->setLocale('en');
        $request->attributes->set('_locale', 'en');

        $event    = new GetResponseEvent($this->getMock(HttpKernelInterface::class), $request, HttpKernelInterface::SUB_REQUEST);
        $listener = new LocaleCookieMatchingListener('en');

        $listener->onKernelRequest($event);

        $this->assertSame('en', $request->getLocale());
        $this->assertSame('en', $request->attributes->get('_locale'));
    }
}
