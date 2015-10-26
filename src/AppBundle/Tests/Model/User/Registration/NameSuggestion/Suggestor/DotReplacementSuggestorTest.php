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

use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\DotReplacementSuggestor;

class DotReplacementSuggestorTest extends \PHPUnit_Framework_TestCase
{
    public function testReplaceSpecialChars()
    {
        $name      = 'Ma.27';
        $suggestor = new DotReplacementSuggestor();
        $result    = $suggestor->getPossibleSuggestions($name);

        $this->assertContains('Ma_27', $result);
        $this->assertCount(1, $result);
    }

    public function testReplaceMultipleSpecialChars()
    {
        $name      = 'M_a.2-7';
        $suggestor = new DotReplacementSuggestor();
        $result    = $suggestor->getPossibleSuggestions($name);

        $this->assertContains('M-a_2_7', $result);
    }

    public function testNoSuggestions()
    {
        $name      = 'Ma27';
        $suggestor = new DotReplacementSuggestor();

        $this->assertCount(0, $suggestor->getPossibleSuggestions($name));
    }

    public function testMultipleBehind()
    {
        $name      = 'M_.-a...2.7__';
        $suggestor = new DotReplacementSuggestor();

        $this->assertContains('M-__a___2_7--', $suggestor->getPossibleSuggestions($name));
    }
}
