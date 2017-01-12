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

namespace AppBundle\Tests\Unit\Model\User\Util\NameSuggestion\Suggestor;

use AppBundle\Model\User\Util\NameSuggestion\Suggestor\YearPostfixSuggestor;

class YearPostfixSuggestorTest extends \PHPUnit_Framework_TestCase
{
    public function testSuggestYears(): void
    {
        $name        = 'benbieler';
        $suggestor   = new YearPostfixSuggestor();
        $suggestions = $suggestor->getPossibleSuggestions($name);
        $year        = date('Y');

        $this->assertContains(sprintf('%s%d', $name, $year), $suggestions);
    }

    public function testSuggestOlderYear(): void
    {
        $name        = 'benbieler';
        $suggestor   = new YearPostfixSuggestor();
        $suggestions = $suggestor->getPossibleSuggestions($name);

        $randomYear = $suggestions[1];
        $bareYear   = (int) preg_replace('/^benbieler([0-9]+)$/', '$1', $randomYear);

        $this->assertLessThan(date('Y'), $bareYear);
    }

    public function testYearAsPostfix(): void
    {
        $name        = 'Ma272000';
        $suggestor   = new \AppBundle\Model\User\Util\NameSuggestion\Suggestor\YearPostfixSuggestor();
        $suggestions = $suggestor->getPossibleSuggestions($name);

        $this->assertCount(0, $suggestions);
    }

    public function testAddUnderscore(): void
    {
        $name        = 'Ma27';
        $suggestor   = new YearPostfixSuggestor();
        $suggestions = $suggestor->getPossibleSuggestions($name);
        $year        = date('Y');

        $this->assertContains(sprintf('%s_%d', $name, $year), $suggestions);
    }
}
