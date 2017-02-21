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

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * AddSuggestorToNameSuggestionChainPass.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class AddSuggestorToNameSuggestionChainPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('app.user.registration.name_suggestor')) {
            return;
        }

        $service = $container->getDefinition('app.user.registration.name_suggestor');
        $tag     = 'app.registration.suggestor';
        foreach ($container->findTaggedServiceIds($tag) as $id => $tags) {
            if (count($tags) > 1) {
                throw new \LogicException(sprintf(
                    'The tag `%s` must be declared one time only!',
                    $tag
                ));
            }

            $service->addMethodCall('register', [new Reference($id)]);
        }
    }
}
