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

namespace AppBundle\View;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Interface which contains an API to assemble a response with translatable errors.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface I18nResponseFormatBuilderInterface
{
    /**
     * Builds the response.
     *
     * @param ConstraintViolationListInterface $violations
     * @param bool                             $sortProperties
     * @param bool                             $useAllLanguages
     * @param string[]                         $targetLocales
     * @param string                           $domain
     *
     * @return string[][]
     */
    public function formatTranslatableViolationList(
        ConstraintViolationListInterface $violations,
        bool $sortProperties = true,
        $useAllLanguages = true,
        array $targetLocales = [],
        string $domain = 'messages'
    ): array;
}
