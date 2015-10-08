<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User\Registration\NameSuggestion\Suggestor;

/**
 * Interface that provides a method for suggestion usernames in case of invalid ones.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface SuggestorInterface
{
    /**
     * Returns possible suggestions.
     *
     * @param string $username
     *
     * @return string[]
     */
    public function getPossibleSuggestions($username);
}
