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

namespace AppBundle;

use AppBundle\DependencyInjection\Compiler\AddSuggestorToNameSuggestionChainPass;
use AppBundle\DependencyInjection\Compiler\ConfigureNotificatableServicesPass;
use AppBundle\DependencyInjection\Compiler\ConnectChannelsWithDelegatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ConfigureNotificatableServicesPass());
        $container->addCompilerPass(new ConnectChannelsWithDelegatorPass());
        $container->addCompilerPass(new AddSuggestorToNameSuggestionChainPass());
    }
}
