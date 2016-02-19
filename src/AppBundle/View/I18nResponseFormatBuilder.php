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

namespace AppBundle\View;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Concrete implementation of a formatter which transforms constraint violations into i18n
 * format that can be used in the HTTP response.
 *
 * It converts a violation list to the following format:
 *
 * [
 *   'de' => ['German translation'],
 *   'en' => ['English translation'],
 * ]
 *
 * Furthermore it is possible to sort all the violations by the property which caused the violation:
 *
 * [
 *   'property' => [
 *     [
 *       'de' => ['German translation'],
 *       'en' => ['English translation'],
 *     ]
 *   ],
 *   'another_property' => [
 *     [
 *       // ...
 *     ]
 *   ]
 * ]
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class I18nResponseFormatBuilder implements I18nResponseFormatBuilderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If the target locales parameter is used improperly.
     */
    public function formatTranslatableViolationList(
        ConstraintViolationListInterface $violations,
        $sortProperties = true,
        $useAllLanguages = true,
        array $targetLocales = [],
        $domain = 'messages'
    ) {
        $violationList = iterator_to_array($violations, false);
        $hasLocales    = count($targetLocales) > 0;

        if (!$useAllLanguages && $hasLocales) {
            throw new \InvalidArgumentException(
                'Wrong usage of $targetLocales: If the default locale is the only target, $targetLocales must not have any values!'
            );
        }

        if ($useAllLanguages && !$hasLocales) {
            throw new \InvalidArgumentException(
                'Wrong usage of $targetLocales: If the all locales should be rendered, $targetLocales must be given!'
            );
        }

        $fixtures = [];
        array_walk(
            $violationList,
            function (ConstraintViolation $violation) use (
                $targetLocales,
                $domain,
                $useAllLanguages,
                $sortProperties,
                &$fixtures
            ) {
                // Every translation entry (whether sorted by properties in deeper levels or as top level)
                // should provide a basic structure.
                switch (true) {
                    // If sorted by $targetLanguages, it may look like this:
                    //
                    // [
                    //   'de' => ['Deutscher Text'],
                    //   'en' => ['English text'],
                    // ]
                    //
                    // When sorting by property, the structure will be merged recursively into the property list:
                    //
                    // [
                    //   'property' => [
                    //     // this is the structure merged into the property.
                    //     'de' => ['Deutscher Text'],
                    //     'en' => ['English text'],
                    //   ]
                    // ]
                    case $useAllLanguages:
                        $structure = array_reduce(
                            $targetLocales,
                            function ($carry, $locale) use ($violation, $domain) {
                                if (!isset($carry[$locale])) {
                                    $carry[$locale] = [];
                                }

                                if ($locale === $this->translator->getLocale()) {
                                    $carry[$locale][] = $violation->getMessage();
                                } else {
                                    $messageTemplate = $violation->getMessageTemplate();
                                    $parameters      = $violation->getParameters();

                                    if ($plural = $violation->getPlural()) {
                                        try {
                                            $message = $this->translator->transChoice(
                                                $messageTemplate,
                                                $plural,
                                                $parameters,
                                                $domain,
                                                $locale
                                            );
                                        } catch (\InvalidArgumentException $ex) {
                                            // we do nothing here.
                                            // If the pluralization fails, the default translation method will be used.

                                            $message = $this->translator->trans(
                                                $messageTemplate,
                                                $parameters,
                                                $domain,
                                                $locale
                                            );
                                        }
                                    } else {
                                        $message = $this->translator->trans(
                                            $messageTemplate,
                                            $parameters,
                                            $domain,
                                            $locale
                                        );
                                    }

                                    $carry[$locale][] = $message;
                                }

                                return $carry;
                            },
                            []
                        );
                        break;
                    default:
                        // If the default language is in use and nothing more, the structure looks like this:
                        //
                        // ['Message in the currently selected language']
                        $structure = [$violation->getMessage()];
                }

                if (!$sortProperties) {
                    $fixtures = array_merge_recursive($fixtures, $structure);

                    return;
                }

                $propertyPath = $violation->getPropertyPath();
                if (!isset($fixtures[$violation->getPropertyPath()])) {
                    $fixtures[$violation->getPropertyPath()] = [];
                }

                $fixtures[$propertyPath] = array_merge_recursive($fixtures[$propertyPath], $structure);
            },
            []
        );

        return $fixtures;
    }
}
