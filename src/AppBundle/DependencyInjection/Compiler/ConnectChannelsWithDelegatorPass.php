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
 * ConnectChannelsWithDelegatorPass.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class ConnectChannelsWithDelegatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('app.notification')) {
            return;
        }

        $tagName  = 'app.notificator.channel';
        $channels = [];
        foreach ($container->findTaggedServiceIds($tagName) as $id => $tags) {
            if (count($tags) > 1) {
                throw new \LogicException(sprintf(
                    'The tag `%s` can be declared one time only!',
                    $tagName
                ));
            }

            if (!array_key_exists('alias', $tags[0])) {
                throw new \LogicException(sprintf(
                    'The attribute "alias" is missing in the tag defintion "%s" of service "%s"!',
                    $tagName,
                    $id
                ));
            }

            $channels[$tags[0]['alias']] = new Reference($id);
        }

        $container->getDefinition('app.notification')->replaceArgument(1, $channels);
    }
}
