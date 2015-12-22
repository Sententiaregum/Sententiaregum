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

namespace AppBundle;

use AppBundle\DependencyInjection\Compiler\RegisterDoctrineRepositoriesPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterDoctrineRepositoriesPass());

        // the extension classes of symfony2 were made in order to provide a flexible
        // and complex solution in order to process complex configuration and create a flexible service layer.
        // In this case we simply need to apply config files and this can be done in the build() method, too, as it
        // will be executed right after the extension will be loaded if available.
        $fileLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));

        foreach (['services', 'validators', 'redis', 'listeners', 'repositories'] as $baseName) {
            $fileLoader->load(sprintf('%s.yml', $baseName));
        }
    }
}
