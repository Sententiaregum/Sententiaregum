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

namespace AppBundle\Composer;

/**
 * Handler that determines whether that's the initial installation.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class PreInstallHandler
{
    /**
     * @var bool
     */
    public static $firstInstall = false;

    /**
     * Composer magic:
     *   Checks whether this is the first install in order to create the schema only the first time.
     */
    public static function determineFirstInstall()
    {
        if (!is_dir(sprintf('%s/../../../vendor', __DIR__))) {
            self::$firstInstall = true;
        }
    }
}
