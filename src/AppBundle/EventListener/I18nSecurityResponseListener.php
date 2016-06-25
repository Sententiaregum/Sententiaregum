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

namespace AppBundle\EventListener;

use Ma27\ApiKeyAuthenticationBundle\Event\AssembleResponseEvent;
use Ma27\ApiKeyAuthenticationBundle\Ma27ApiKeyAuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Listener for the response lifecycle of the apikey bundle.
 * This listener transforms a deeper structure with translations.
 *
 * 'Message' -> ['de' => ['Meldung'], 'en' => ['Message']]
 *
 * This is useful for the i18n system of the frontend which needs translations of the security errors.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class I18nSecurityResponseListener implements EventSubscriberInterface
{
    const AUTH_DEFAULT_ERROR = 'BACKEND_AUTH_FAILURE';
    const TRANSLATION_DOMAIN = 'messages';

    /**
     * @var mixed[]
     */
    private $locales = [];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param string[]            $locales
     * @param TranslatorInterface $translator
     */
    public function __construct(array $locales, TranslatorInterface $translator)
    {
        $this->locales    = $locales;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Ma27ApiKeyAuthenticationEvents::ASSEMBLE_RESPONSE => ['onResponseCreation'],
        ];
    }

    /**
     * Handler for the response creation.
     *
     * @param AssembleResponseEvent $event
     */
    public function onResponseCreation(AssembleResponseEvent $event)
    {
        if ($event->isSuccess()) {
            return;
        }

        $error      = $event->getException()->getMessage();
        $translator = $this->translator;
        $data       = array_combine(
            $this->locales,
            array_map(function ($locale) use ($translator, $error) {
                return $translator->trans($error ?: static::AUTH_DEFAULT_ERROR, [], static::TRANSLATION_DOMAIN, $locale);
            }, $this->locales)
        );

        $event->setResponse($this->createJSONResponseFromTranslationList($data));
        $event->stopPropagation();
    }

    /**
     * Builds the json response for the i18n translation list.
     *
     * @param array $data
     *
     * @return JsonResponse
     */
    private function createJSONResponseFromTranslationList(array $data): JsonResponse
    {
        return new JsonResponse(
            ['message' => $data],
            JsonResponse::HTTP_UNAUTHORIZED
        );
    }
}
