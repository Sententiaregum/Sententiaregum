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

namespace AppBundle\Model\User\Util\NameSuggestion\Suggestor;

/**
 * Interface that provides a method for suggestion usernames in case of invalid ones.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface SuggestorInterface
{
    /**
     * Returns possible suggestions.
     *
     * @param string $username
     *
     * @return string[]
     */
    public function getPossibleSuggestions(string $username): array;
}
