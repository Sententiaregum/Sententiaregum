<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Model\User\Registration\NameSuggestion\Suggestor;

use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\YearPostfixSuggestor;

class YearPostfixSuggestorTest extends \PHPUnit_Framework_TestCase
{
    public function testSuggestYears()
    {
        $name        = 'benbieler';
        $suggestor   = new YearPostfixSuggestor();
        $suggestions = $suggestor->getPossibleSuggestions($name);
        $year        = date('Y');

        $this->assertContains(sprintf('%s%d', $name, $year), $suggestions);
    }

    public function testSuggestOlderYear()
    {
        $name        = 'benbieler';
        $suggestor   = new YearPostfixSuggestor();
        $suggestions = $suggestor->getPossibleSuggestions($name);

        $randomYear = $suggestions[1];
        $bareYear   = (integer) preg_replace('/^benbieler([0-9]+)$/', '$1', $randomYear);

        $this->assertLessThan(date('Y'), $bareYear);
    }

    public function testYearAsPostfix()
    {
        $name        = 'Ma272000';
        $suggestor   = new YearPostfixSuggestor();
        $suggestions = $suggestor->getPossibleSuggestions($name);

        $this->assertCount(0, $suggestions);
    }

    public function testAddUnderscore()
    {
        $name        = 'Ma27';
        $suggestor   = new YearPostfixSuggestor();
        $suggestions = $suggestor->getPossibleSuggestions($name);
        $year        = date('Y');

        $this->assertContains(sprintf('%s_%d', $name, $year), $suggestions);
    }
}
