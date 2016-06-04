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

namespace AppBundle\Tests\Functional\API\Locale;

use AppBundle\Tests\Functional\BaseTrait;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Context for the locale provider.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ProviderContext implements SnippetAcceptingContext
{
    use BaseTrait;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @When I'd like to see all locales with display names
     */
    public function iWouldLikeToSeeAllLocalesWithDisplayNames()
    {
        $this->result = $this->performRequest('GET', '/api/locale.json');
    }

    /**
     * @Then I should see the following locales:
     */
    public function iShouldSeeTheFollowingLocales(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            Assertion::keyExists($this->result, $row['shortcut']);
            Assertion::eq($row['display_name'], $this->result[$row['shortcut']]);
        }

        Assertion::count(iterator_to_array($table), count($this->result));
    }
}
