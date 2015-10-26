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
 * Suggestor which replaces special chars with other ones.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class DotReplacementSuggestor implements SuggestorInterface
{
    const SPECIAL_CHAR_MATCHING_REGEX = '/([\.\_\-]+)/';

    /**
     * {@inheritdoc}
     */
    public function getPossibleSuggestions($username)
    {
        $result = [];
        if ($newName = $this->replaceSpecialCharsForUsername($username) !== $username) {
            $result[] = $newName;
        }

        return $result;
    }

    /**
     * Replaces the special characters for the username.
     *
     * @param string $username
     *
     * @return string
     */
    private function replaceSpecialCharsForUsername($username)
    {
        return preg_replace_callback(
            self::SPECIAL_CHAR_MATCHING_REGEX,
            function ($matches) {
                return '_' === $matches[0] ? '-' === $matches[0] ? '.' : '-' : '_';
            },
            $username
        );
    }
}
