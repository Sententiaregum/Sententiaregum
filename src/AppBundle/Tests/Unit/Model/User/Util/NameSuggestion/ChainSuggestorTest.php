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

namespace AppBundle\Tests\Unit\Model\User\Util\NameSuggestion;

use AppBundle\Model\User\UserReadRepositoryInterface;
use AppBundle\Model\User\Util\NameSuggestion\ChainSuggestor;
use AppBundle\Model\User\Util\NameSuggestion\Suggestor\DotReplacementSuggestor;
use AppBundle\Model\User\Util\NameSuggestion\Suggestor\YearPostfixSuggestor;

class ChainSuggestorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoResults()
    {
        $repository = $this->getMockWithoutInvokingTheOriginalConstructor(UserReadRepositoryInterface::class);
        $repository
            ->expects($this->never())
            ->method('filterUniqueUsernames');

        $suggestor = new ChainSuggestor($repository);
        $suggestor->register(new YearPostfixSuggestor());
        $suggestor->register(new DotReplacementSuggestor());
        $this->assertCount(0, $suggestor->getPossibleSuggestions('foo2016'));
    }

    public function testFilterResults()
    {
        $repository = $this->getMockWithoutInvokingTheOriginalConstructor(UserReadRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('filterUniqueUsernames')
            ->willReturn(['ma.27']);

        $suggestor = new ChainSuggestor($repository);
        $suggestor->register(new YearPostfixSuggestor());
        $suggestor->register(new DotReplacementSuggestor());

        $result = $suggestor->getPossibleSuggestions('ma_27');

        $this->assertCount(1, $result);
        $this->assertContains('ma.27', $result);
    }

    /**
     * Test case to avoid regression with array_merge() which expects >=2 parameters.
     */
    public function testNoSuggestors()
    {
        $repository = $this->getMockWithoutInvokingTheOriginalConstructor(UserReadRepositoryInterface::class);
        $repository
            ->expects(self::never())
            ->method('filterUniqueUsernames');

        $suggestor = new ChainSuggestor($repository);
        self::assertCount(0, $suggestor->getPossibleSuggestions('Ma27'));
    }
}
