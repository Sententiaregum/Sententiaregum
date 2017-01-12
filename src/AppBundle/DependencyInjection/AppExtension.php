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

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * AppExtension.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class AppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $fileLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (['services', 'validators', 'redis', 'listeners', 'repositories', 'commands', 'handlers', 'middlewares', 'suggestors'] as $baseName) {
            $fileLoader->load(sprintf('%s.yml', $baseName));
        }
    }
}
