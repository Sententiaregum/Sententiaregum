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

namespace AppBundle\Composer;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as AbstractScriptHandler;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Handler that is responsible for certain install tasks (e.g. database schema setup, fixture appliance or frontend preparation).
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class ScriptHandler extends AbstractScriptHandler
{
    /**
     * Installs the npm dependencies.
     *
     * @param Event $event
     */
    public static function installNpmDependencies(Event $event): void
    {
        if (isset($_ENV['SKIP_NPM_INSTALL'])) {
            return;
        }

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
    public static function buildFrontendData(Event $event): void
    {
        $devMode = $event->isDevMode();
        self::executeNpmCommand(
            sprintf('run %s', $devMode ? 'dev' : 'prod'),
            $event,
            $devMode,
            500
        );
    }

    /**
     * Creates the doctrine schema.
     *
     * @param Event $event
     */
    public static function createDoctrineSchema(Event $event): void
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
     */
    private static function executeNpmCommand(string $command, Event $event, bool $showOutput = true, int $timeout = 500): void
    {
        $npm         = (new ExecutableFinder())->find('npm');
        $fullCommand = sprintf('%s %s %s', null, $npm, $command);
        $handler     = function ($type, $buffer) use ($event): void {
            $event->getIO()->write($buffer, false);
        };

        (new Process($fullCommand, null, null, null, $timeout))
            ->run($showOutput ? $handler : function () {
            });
    }
}
