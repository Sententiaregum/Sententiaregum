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

namespace AppBundle\Tests\Unit\Model\User\Util\NameSuggestion\Suggestor;

use AppBundle\Model\User\Util\NameSuggestion\Suggestor\DotReplacementSuggestor;

class DotReplacementSuggestorTest extends \PHPUnit_Framework_TestCase
{
    public function testReplaceSpecialChars(): void
    {
        $name      = 'Ma.27';
        $suggestor = new DotReplacementSuggestor();
        $result    = $suggestor->getPossibleSuggestions($name);

        $this->assertContains('Ma_27', $result);
        $this->assertCount(1, $result);
    }

    public function testReplaceDash(): void
    {
        $name      = 'Ma-27';
        $suggestor = new DotReplacementSuggestor();
        $result    = $suggestor->getPossibleSuggestions($name);

        $this->assertContains('Ma_27', $result);
        $this->assertCount(1, $result);
    }

    public function testReplaceMultipleSpecialChars(): void
    {
        $name      = 'M_a.2-7';
        $suggestor = new DotReplacementSuggestor();
        $result    = $suggestor->getPossibleSuggestions($name);

        $this->assertContains('M-a_2_7', $result);
    }

    public function testNoSuggestions(): void
    {
        $name      = 'Ma27';
        $suggestor = new DotReplacementSuggestor();

        $this->assertCount(0, $suggestor->getPossibleSuggestions($name));
    }

    public function testMultipleBehind(): void
    {
        $name      = 'M_.-a...2.7__';
        $suggestor = new DotReplacementSuggestor();

        $this->assertContains('M_a_2_7_', $suggestor->getPossibleSuggestions($name));
    }
}
