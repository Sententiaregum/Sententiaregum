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

namespace AppBundle\Tests\Acceptance\Integration\AggregateQuerying\User;

use AppBundle\Model\User\Role;
use AppBundle\Tests\Acceptance\AbstractIntegrationContext;
use Assert\Assertion;

/**
 * Behat context for role model interactions.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
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
    public function determineDefaultRole(): void
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
    public function checkRole($arg1): void
    {
        Assertion::eq($this->resultRole->getRole(), $arg1);
    }

    /**
     * @Then I should get an error
     */
    public function ensureError(): void
    {
        Assertion::isInstanceOf($this->exception, \RuntimeException::class);
        Assertion::eq($this->exception->getMessage(), 'Role "ROLE_USER" is not present!');
    }
}
