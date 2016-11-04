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

namespace AppBundle\Tests\Acceptance\Integration\AggregateQuerying\User;

use AppBundle\Model\User\Role;
use AppBundle\Tests\Acceptance\AbstractIntegrationContext;
use AppBundle\Tests\Functional\FixtureLoadingContext;
use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Behat context for role model interactions.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class RoleContext extends AbstractIntegrationContext
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var Role
     */
    private $resultRole;

    /**
     * @When I determine the default role
     */
    public function determineDefaultRole()
    {
        try {
            $this->resultRole = $this->getEntityManager()->getRepository('Account:Role')->determineDefaultRole();
        } catch (\Exception $ex) {
            $this->exception = $ex;
        }
    }

    /**
     * @Then I should get the :arg1 role
     */
    public function checkRole($arg1)
    {
        Assertion::eq($this->resultRole->getRole(), $arg1);
    }

    /**
     * @Then I should get an error
     */
    public function ensureError()
    {
        Assertion::isInstanceOf($this->exception, \RuntimeException::class);
        Assertion::eq($this->exception->getMessage(), 'Role "ROLE_USER" is not present!');
    }
}
