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

namespace AppBundle\Tests\Acceptance;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;

/**
 * CLIContext.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class CLIContext extends AbstractIntegrationContext
{
    /**
     * @Given /^the database is purged$/
     */
    public function ensurePurgedDB(): void
    {
        (new ORMPurger($this->getEntityManager()))->purge();
    }
}
