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

namespace AppBundle\Service\Doctrine\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * Marker interface that tells doctrine that that's a special interface made for the prod environment only.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
interface ProductionFixtureInterface extends FixtureInterface
{
}
