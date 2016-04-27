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
use Behat\Gherkin\Node\TableNode;

/**
 * Context for the locale API.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LocaleContext extends FixtureLoadingContext implements SnippetAcceptingContext
{
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

    /**
     * @When I try to change my locale to :arg1
     */
    public function iTryToChangeMyLocaleTo($arg1)
    {
        $this->result = $this->performRequest(
            'PATCH',
            '/api/protected/locale.json',
            ['locale' => $arg1],
            null,
            [],
            [],
            null,
            false,
            null,
            true
        );
    }

    /**
     * @Then I should get an error
     */
    public function iShouldGetAnError()
    {
        Assertion::eq(400, $this->result->getStatusCode());
    }

    /**
     * @Then the locale should be changed
     */
    public function theLocaleShouldBeChanged()
    {
        $user = $this->getEntityManager()->getRepository('Account:User')->findOneBy(['username' => 'Ma27']);
        Assertion::eq('en', $user->getLocale());
    }

    /**
     * @Then a cookie should be set
     */
    public function aCookieShouldBeSet()
    {
        $cookie = $this->result->headers->getCookies()[0];

        Assertion::eq('en', $cookie->getValue());
        Assertion::eq('language', $cookie->getName());
    }
}
