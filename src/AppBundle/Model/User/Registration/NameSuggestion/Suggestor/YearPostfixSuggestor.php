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
    public function getPossibleSuggestions($username)
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
    private function getCurrentYearAsString()
    {
        return (string) $this->getCurrentYear();
    }

    /**
     * Returns a random year.
     *
     * @return string
     */
    private function getRandomYearThatIsLessThanTheCurrentYear()
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
    private function getCurrentYear()
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
    private function isNumberLastChar($username, $amount = 1)
    {
        return true === (bool) preg_match(sprintf(self::YEAR_MATCHING_REGEX, $amount), $username);
    }
}
