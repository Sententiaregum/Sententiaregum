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

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler which configures the notification system and connects it to the command handlers.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ConfigureNotificatableCommandHandlersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \LogicException If the tag is declared multiple times or the `template` attribute is missing.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('app.notification')) {
            return;
        }

        $templateMap = [];
        $notificator = $container->getDefinition('app.notification');
        $tagName     = 'app.command_handler.notificatable';
        foreach ($container->findTaggedServiceIds($tagName) as $id => $tags) {
            if (count($tags) > 1) {
                throw new \LogicException(sprintf(
                    'The tag `%s` must be declared one time only!',
                    $tagName
                ));
            }

            $tagData = $tags[0];
            if (!isset($tagData['template'])) {
                throw new \LogicException(sprintf(
                    'The tag `%s` on service "%s" is missing the attribute `template`!',
                    $tagName,
                    $id
                ));
            }

            $definition                           = $container->getDefinition($id);
            $templateMap[$definition->getClass()] = $tagData['template'];

            $definition->addMethodCall('setNotificator', [new Reference('app.notification')]);
        }

        $notificator->replaceArgument(1, $templateMap);
    }
}
