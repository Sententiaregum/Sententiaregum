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

namespace AppBundle\Tests\Unit\Model\User\Registration\NameSuggestion\Suggestor;

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
        $bareYear   = (int) preg_replace('/^benbieler([0-9]+)$/', '$1', $randomYear);

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
