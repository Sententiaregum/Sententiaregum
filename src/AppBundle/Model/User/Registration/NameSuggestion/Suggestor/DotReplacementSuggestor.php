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

namespace AppBundle\Model\User\Registration\NameSuggestion\Suggestor;

/**
 * Suggestor which replaces special chars with other ones.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class DotReplacementSuggestor implements SuggestorInterface
{
    const SPECIAL_CHAR_MATCHING_REGEX = '/([\._\-]+)/';

    /**
     * {@inheritdoc}
     */
    public function getPossibleSuggestions($username)
    {
        $result = [];
        if ($username !== $newName = $this->replaceSpecialCharsForUsername($username)) {
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
                return '_' === $matches[0] ? ('-' === $matches[0] ? '.' : '-') : '_';
            },
            $username
        );
    }
}
