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
    /**
     * {@inheritdoc}
     */
    public function getPossibleSuggestions($username)
    {
        $result = [];

        if (false === (bool) preg_match('#([0-9]+){4}$#', $username)) {
            $result = [
                $username.$this->getCurrentYearAsString(),
                $username.$this->getRandomYearThatIsLessThanTheCurrentYear()
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
}
