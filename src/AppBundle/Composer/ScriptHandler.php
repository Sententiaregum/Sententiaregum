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
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Handler that runs the npm installation after the composer install.
 */
class ScriptHandler
{
    /**
     * Installs the npm dependencies.
     *
     * @param CommandEvent $event
     */
    public static function installNpmDependencies(CommandEvent $event)
    {
        (new Process(sprintf('%s install --no-bin-links', (new ExecutableFinder())->find('npm')), null, null, null, 1000))->run(
            function ($type, $buffer) use ($event) { $event->getIO()->write($buffer, false); }
        );
    }
}
