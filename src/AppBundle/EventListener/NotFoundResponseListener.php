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

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * HTTP listener which handles 404 errors.
 *
 * If the request is an API request, the JSON will be rendered, but if it's an invalid request against the page, it will
 * be redirected to the appropriate hashbang target.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class NotFoundResponseListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * Exception handler.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $uri     = $request->getRequestUri();

        // abort for API requests (fos_rest will take over) and non-404 exceptions
        if (!$event->getException() instanceof NotFoundHttpException
            || (bool) preg_match('/^\/api\/(.*)(\?(.*))?$/', $uri)
        ) {
            return;
        }

        // delegate to hashbangs
        $event->setResponse(new RedirectResponse($this->getHashBangURL($uri)));
    }

    /**
     * Transforms the URI into a hashbang url.
     *
     * @param string $requestUri
     *
     * @return string
     */
    private function getHashBangURL(string $requestUri): string
    {
        // the slash after the `#` is not needed as the request_uri contains it's own slash as prefix
        return sprintf('/#%s', $requestUri);
    }
}
