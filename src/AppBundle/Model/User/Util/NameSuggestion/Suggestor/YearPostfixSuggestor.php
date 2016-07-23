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

namespace AppBundle\Model\User\Util\NameSuggestion\Suggestor;

/**
 * Suggestor that adds years as postfix on usernames.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class YearPostfixSuggestor implements SuggestorInterface
{
    const YEAR_MATCHING_REGEX = '#([0-9]+){%d}$#';

    /**
     * {@inheritdoc}
     */
    public function getPossibleSuggestions(string $username): array
    {
        $result = [];

        if (!$this->isNumberLastChar($username, 4)) {
            if ($this->isNumberLastChar($username)) {
                $username .= '_';
            }

            $result = [
                $username.$this->getCurrentYearAsString(),
                $username.$this->getRandomYearThatIsLessThanTheCurrentYear(),
            ];
        }

        return $result;
    }

    /**
     * Transforms the current year to a string.
     *
     * @return string
     */
    private function getCurrentYearAsString(): string
    {
        return (string) $this->getCurrentYear();
    }

    /**
     * Returns a random year.
     *
     * @return int
     */
    private function getRandomYearThatIsLessThanTheCurrentYear(): int
    {
        $current = $this->getCurrentYear();
        $range   = range($current - 51, $current - 1);

        return $range[mt_rand(0, 50)];
    }

    /**
     * Gets the current year.
     *
     * @return string
     */
    private function getCurrentYear(): string
    {
        return (new \DateTime('now'))->format('Y');
    }

    /**
     * Checks if the last char is a number.
     *
     * @param string $username
     * @param int    $amount
     *
     * @return bool
     */
    private function isNumberLastChar(string $username, int $amount = 1): bool
    {
        return true === (bool) preg_match(sprintf(self::YEAR_MATCHING_REGEX, $amount), $username);
    }
}
