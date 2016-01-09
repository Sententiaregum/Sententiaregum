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

namespace AppBundle\Behat;

use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Context for the username suggestions.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UsernameSuggestionsContext extends BaseContext implements SnippetAcceptingContext
{
    /**
     * @var string[]
     */
    private $suggestions = [];

    /**
     * @When I generate suggestions for :arg1
     */
    public function iGenerateSuggestionsForThisName($arg1)
    {
        $this->suggestions = $this
            ->getContainer()
            ->get('app.user.registration.name_suggestor')
            ->getPossibleSuggestions($arg1);
    }

    /**
     * @Then I should see 2 name suggestions
     */
    public function iShouldSee2NameSuggestions()
    {
        Assertion::count($this->suggestions, 2);
    }
}
