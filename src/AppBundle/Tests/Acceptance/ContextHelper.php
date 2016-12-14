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

namespace AppBundle\Tests\Acceptance;

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * ContextHelper.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class ContextHelper
{
    /**
     * Simple provider for the API context.
     *
     * @param BeforeScenarioScope $scenarioScope
     *
     * @return ApiContext
     */
    public static function connectToAPIContext(BeforeScenarioScope $scenarioScope): ApiContext
    {
        $environment = $scenarioScope->getEnvironment();

        if (!$environment instanceof InitializedContextEnvironment) {
            throw new \RuntimeException(sprintf(
                'Cannot run tests as current feature context needs to access `ApiContext`, but for that "%s" is needed!',
                InitializedContextEnvironment::class
            ));
        }

        return $environment->getContext(ApiContext::class);
    }
}
