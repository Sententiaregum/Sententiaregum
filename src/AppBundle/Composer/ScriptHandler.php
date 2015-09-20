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
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as AbstractScriptHandler;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Handler that runs the npm installation after the composer install.
 */
class ScriptHandler extends AbstractScriptHandler
{
    /**
     * Installs the npm dependencies.
     *
     * @param CommandEvent $event
     */
    public static function installNpmDependencies(CommandEvent $event)
    {
        $npm     = (new ExecutableFinder())->find('npm');
        $handler = function ($type, $buffer) use ($event) {
            $event->getIO()->write($buffer, false);
        };

        $process = new Process(sprintf('%s install --no-bin-links', $npm, null, null, null, 1000));
        $process->run($handler);
    }

    /**
     * Loads the doctrine data fixtures (disabled when using the "--no-dev" flag)
     *
     * @param CommandEvent $event
     */
    public static function loadDoctrineDataFixtures(CommandEvent $event)
    {
        if ($event->isDevMode()) {
            static::executeCommand(
                $event,
                static::getConsoleDir($event, 'load data fixtures'),
                'doctrine:fixtures:load --no-interaction'
            );
        }
    }

    /**
     * Creates the doctrine schema
     *
     * @param CommandEvent $event
     */
    public static function createDoctrineSchema(CommandEvent $event)
    {
        static::executeCommand(
            $event,
            static::getConsoleDir($event, 'create doctrine schema'),
            'doctrine:schema:create'
        );
    }

    /**
     * Updates the doctrine schema
     *
     * @param CommandEvent $event
     */
    public static function updateDoctrineSchema(CommandEvent $event)
    {
        static::executeCommand(
            $event,
            static::getConsoleDir($event, 'update doctrine schema'),
            'doctrine:schema:update --force'
        );
    }
}
