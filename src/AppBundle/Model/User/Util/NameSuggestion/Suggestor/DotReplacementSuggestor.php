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

namespace AppBundle\Model\User\Util\NameSuggestion\Suggestor;

/**
 * Suggestor which replaces special chars with other ones.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
final class DotReplacementSuggestor implements SuggestorInterface
{
    const SPECIAL_CHAR_MATCHING_REGEX = '/([\._\-]+)/';

    /**
     * {@inheritdoc}
     */
    public function getPossibleSuggestions(string $username): array
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
    private function replaceSpecialCharsForUsername(string $username): string
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
