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
class ConfigureNotificatableServicesPass implements CompilerPassInterface
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
        $tagName     = 'app.service.notificatable';
        foreach ($container->findTaggedServiceIds($tagName) as $id => $tags) {
            if (count($tags) > 1) {
                throw new \LogicException(sprintf(
                    'The tag `%s` must be declared one time only!',
                    $tagName
                ));
            }

            $definition = $container->getDefinition($id);
            if (array_key_exists('template', $tags[0])) {
                $templateMap[$definition->getClass()] = $tags[0]['template'];
            }

            $definition->addMethodCall('setNotificator', [new Reference('app.notification')]);
        }

        $notificator->replaceArgument(0, $templateMap);
    }
}
