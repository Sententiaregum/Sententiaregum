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
        $sortProperties = true,
        $useAllLanguages = true,
        array $targetLocales = [],
        $domain = 'messages'
    );
}
