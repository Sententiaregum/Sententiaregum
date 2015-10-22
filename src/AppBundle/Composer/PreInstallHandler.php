<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Composer;

use Composer\Script\CommandEvent;

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
     *
     * @param CommandEvent $event
     */
    public static function determineFirstInstall(CommandEvent $event)
    {
        if (!is_dir(__DIR__.'/../../../vendor')) {
            self::$firstInstall = true;
        }
    }
}
