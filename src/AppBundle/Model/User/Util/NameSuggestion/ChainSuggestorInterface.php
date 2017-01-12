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

namespace AppBundle\Model\User\Util\NameSuggestion;

use AppBundle\Model\User\Util\NameSuggestion\Suggestor\SuggestorInterface;

/**
 * Chainable suggestion strategy.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface ChainSuggestorInterface extends SuggestorInterface
{
    /**
     * Adds a new suggestor.
     *
     * @param SuggestorInterface $suggestor
     *
     * @return ChainSuggestorInterface
     */
    public function register(SuggestorInterface $suggestor): self;
}
