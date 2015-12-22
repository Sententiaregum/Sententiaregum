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

namespace AppBundle\EventListener;

use AppBundle\Model\User\User;
use AppBundle\Model\User\UserRepository;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Listener which fixes the language cookie after login.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LanguageCookieFixerListener
{
    /**
     * @var \AppBundle\Model\User\UserRepository
     */
    private $userRepository;

    /**
     * Constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Hook into the kernel process.
     *
     * @param FilterResponseEvent $event
     */
    public function onResponseFilter(FilterResponseEvent $event)
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        if ($this->isProperRequest($request, $response)) {
            $apiKey = $this->extractApiKeyFromResponse($response);
            $user   = $this->userRepository->findOneBy(['apiKey' => $apiKey]);
            if (!$user) {
                throw new \InvalidArgumentException(sprintf('Cannot find user for api key "%s"!', $apiKey));
            }

            $this->fixCookie($user, $request, $response);
        }
    }

    /**
     * Checks whether this is the correct request.
     * This hook should be only executed after the login route and only
     * if the login succeeded.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     */
    private function isProperRequest(Request $request, Response $response)
    {
        return $request->attributes->get('_route') === 'ma27_api_key_authentication.request'
            && Codes::HTTP_OK === $response->getStatusCode();
    }

    /**
     * Extracts the api key from the response.
     *
     * @param Response $response
     *
     * @throws \InvalidArgumentException If the key does not exist.
     *
     * @return string
     */
    private function extractApiKeyFromResponse(Response $response)
    {
        $decoded = json_decode($response->getContent(), true);
        if (self::isJsonDecodeFailed($decoded)) {
            throw new \InvalidArgumentException($this->getJsonDecodeExceptionMessage());
        }

        return $decoded['apiKey'];
    }

    /**
     * Fixes the language cookie if the user's language doesn't match the default one.
     *
     * @param User     $user
     * @param Request  $request
     * @param Response $response
     */
    private function fixCookie(User $user, Request $request, Response $response)
    {
        if ($request->cookies->get('language') === $locale = $user->getLocale()) {
            return;
        }

        $response->headers->setCookie(new Cookie('language', $locale));
    }

    /**
     * Creates an exception message if the json decode failed.
     *
     * @return string
     */
    private function getJsonDecodeExceptionMessage()
    {
        $basicMessage = 'Cannot extract api key from response!';

        if ('No error' !== $message = json_last_error_msg()) {
            $basicMessage = substr($basicMessage, 0, -1);

            $basicMessage .= sprintf(
                ' due to the following error: "%s"!',
                $message
            );
        }

        return $basicMessage;
    }

    /**
     * Checks whether the decode failed.
     *
     * @param mixed $result
     *
     * @return bool
     */
    private static function isJsonDecodeFailed($result)
    {
        return !$result && json_last_error() !== JSON_ERROR_NONE
            || !isset($result['apiKey']);
    }
}
