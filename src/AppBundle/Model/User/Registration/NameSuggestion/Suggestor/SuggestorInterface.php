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

namespace AppBundle\Model\User\Registration\NameSuggestion\Suggestor;

/**
 * Interface that provides a method for suggestion usernames in case of invalid ones.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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
    public function getPossibleSuggestions($username);
}
