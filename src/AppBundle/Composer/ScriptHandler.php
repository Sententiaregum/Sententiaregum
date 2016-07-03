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

namespace AppBundle\Composer;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as AbstractScriptHandler;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Handler that is responsible for certain install tasks (e.g. database schema setup, fixture appliance or frontend preparation).
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ScriptHandler extends AbstractScriptHandler
{
    /**
     * Installs the npm dependencies.
     *
     * @param Event $event
     */
    public static function installNpmDependencies(Event $event)
    {
        $cmd = 'install';
        if (!$event->isDevMode()) {
            $cmd .= ' --production';
        }

        self::executeNpmCommand($cmd, $event, $event->isDevMode(), 1000);
    }

    /**
     * Runs the frontend build.
     *
     * @param Event $event
     */
    public static function buildFrontendData(Event $event)
    {
        $devMode = $event->isDevMode();
        self::executeNpmCommand(
            'run frontend',
            $event,
            $devMode,
            500,
            $devMode ? 'development' : 'production'
        );
    }

    /**
     * Creates the doctrine schema.
     *
     * @param Event $event
     */
    public static function createDoctrineSchema(Event $event)
    {
        $envs = $event->isDevMode() ? ['dev', 'test'] : ['prod'];

        foreach ($envs as $env) {
            $cmd = sprintf('sententiaregum:install:database --no-interaction --apply-fixtures --env=%s', $env);

            if (!$event->isDevMode()) {
                $cmd .= ' --production-fixtures -s migrations';
            }

            static::executeCommand(
                $event,
                static::getConsoleDir($event, 'create doctrine schema'),
                $cmd
            );
        }
    }

    /**
     * Method which executes npm commands.
     *
     * @param string    $command
     * @param Event     $event
     * @param bool|true $showOutput
     * @param int       $timeout
     * @param string    $nodeEnv
     */
    private static function executeNpmCommand(string $command, Event $event, bool $showOutput = true, int $timeout = 500, string $nodeEnv = null)
    {
        $npm         = (new ExecutableFinder())->find('npm');
        $fullCommand = sprintf('%s %s %s', $nodeEnv ? sprintf('NODE_ENV=%s', $nodeEnv) : null, $npm, $command);
        $handler     = function ($type, $buffer) use ($event) {
            $event->getIO()->write($buffer, false);
        };

        (new Process($fullCommand, null, null, null, $timeout))
            ->run($showOutput ? $handler : function () {
            });
    }
}
