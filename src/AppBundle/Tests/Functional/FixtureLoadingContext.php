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

namespace AppBundle\Tests\Functional;

use AppBundle\DataFixtures\ORM\AdminFixture;
use AppBundle\DataFixtures\ORM\RoleFixture;
use AppBundle\DataFixtures\ORM\UserFixture;
use Behat\Symfony2Extension\Context\KernelAwareContext;

/**
 * Base context that contains basic features of every behat context.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
abstract class FixtureLoadingContext implements KernelAwareContext
{
    use BaseTrait;

    /**
     * @var bool
     */
    protected static $applyUserFixtures = true;

    /**
     * @var bool
     */
    protected static $applyFixtures = true;

    /** @BeforeScenario */
    public function loadDataFixtures()
    {
        if (static::$applyFixtures) {
            /** @var \AppBundle\Doctrine\ConfigurableFixturesLoader $service */
            $service  = $this->getContainer()->get('app.doctrine.fixtures_loader');
            $fixtures = [RoleFixture::class];

            if (static::$applyUserFixtures) {
                $fixtures[] = AdminFixture::class;
                $fixtures[] = UserFixture::class;
            }
            $service->applyFixtures($fixtures);
        }
    }
}
