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

namespace AppBundle\Tests\Acceptance\Functional\Locale;

use AppBundle\Model\User\User;
use Assert\Assertion;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

/**
 * Context for the locale API.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class SwitcherContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @Then /^I should have an account with name "(.*)" and locale "(.*)"$/
     *
     * @param string $username
     * @param string $locale
     */
    public function ensureUserLocale(string $username, string $locale): void
    {
        $actual = $this->retrieveUserByName($username)->getLocale();

        Assertion::eq($locale, $actual, sprintf(
            'User locale ("%s") and actual locale ("%s") don\'t match!',
            $actual,
            $locale
        ));
    }

    /**
     * Simple helper to retrieve a user by its name.
     *
     * @param string $username
     *
     * @return User
     */
    private function retrieveUserByName(string $username): User
    {
        return $this->getContainer()->get('app.repository.user')
            ->findOneBy(['username' => $username]);
    }
}
