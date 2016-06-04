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

namespace AppBundle\Tests\Unit\Model\User\Registration\NameSuggestion;

use AppBundle\Model\User\Registration\NameSuggestion\ChainSuggestor;
use AppBundle\Model\User\UserRepository;

class ChainSuggestorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoResults()
    {
        $repository = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);
        $repository
            ->expects($this->never())
            ->method('filterUniqueUsernames');

        $suggestor = new ChainSuggestor($repository);
        $this->assertCount(0, $suggestor->getPossibleSuggestions('foo2016'));
    }

    public function testFilterResults()
    {
        $repository = $this->getMockWithoutInvokingTheOriginalConstructor(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('filterUniqueUsernames')
            ->willReturn(['ma.27']);

        $suggestor = new ChainSuggestor($repository);
        $result    = $suggestor->getPossibleSuggestions('ma_27');

        $this->assertCount(1, $result);
        $this->assertContains('ma.27', $result);
    }
}
