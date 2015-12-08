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

use AppBundle\Command\PurgeOutdatedPendingActivationsCommand;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Context for the user purger feature.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UserPurgerContext extends BaseContext implements SnippetAcceptingContext
{
    /**
     * @var bool
     */
    protected static $applyUserFixtures = false;

    /**
     * @var string
     */
    private $display;

    /**
     * @When I trigger the command to remove all users having a pending activation
     */
    public function iTriggerTheCommandToRemoveAllUsersHavingAPendingActivation()
    {
        $application = new Application($this->getKernel());
        $application->add(new PurgeOutdatedPendingActivationsCommand());

        $tester = new CommandTester($application->find('sententiaregum:purge:pending-activations'));

        $tester->execute([]);
        $this->display = $tester->getDisplay();
    }

    /**
     * @Then All users with a pending and outdated activation should be removed
     */
    public function allUsersWithAPendingAndOutdatedActivationShouldBeRemoved()
    {
        $em = $this->getEntityManager();

        for ($i = 0; $i < 2; ++$i) {
            Assertion::eq(null, $em->getRepository('Account:User')->findOneBy(['username' => (string) $i]));
        }

        Assertion::notEq(null, $em->getRepository('Account:User')->findOneBy(['username' => 'foo']));
    }

    /**
     * @Then I should see the amount of purged users
     */
    public function iShouldSeeTheAmountOfPurgedUsers()
    {
        Assertion::contains($this->display, 'Successfully purged 2 pending activations');
    }
}
