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

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Pass that completes the configuration of doctrine repositories automatically.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class RegisterDoctrineRepositoriesPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $tag;

    /**
     * Constructor.
     *
     * @param string $tag
     */
    public function __construct($tag = 'app.tag.repository')
    {
        $this->tag = (string) $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds($this->tag);

        foreach ($taggedServices as $serviceId => $config) {
            $definition = $container->getDefinition($serviceId);
            if (count($config) > 1) {
                // a service can be tagged multiple times with one tag.
                // in this use case it does not make sense, so an exception will be thrown
                throw new \LogicException(sprintf(
                    'The service "%s" cannot be tagged with "%s" more than one time!',
                    $serviceId,
                    $this->tag
                ));
            }

            $repoConfig             = array_shift($config);
            $entityManagerServiceId = isset($repoConfig['manager'])
                ? $repoConfig['manager']
                : 'doctrine.orm.default_entity_manager';

            if (!$container->hasDefinition($entityManagerServiceId)) {
                throw new \LogicException(sprintf(
                    'The given entity manager service id "%s" does not exist in the container!',
                    $entityManagerServiceId
                ));
            }

            if (0 < count($definition->getArguments())) {
                throw new \LogicException(sprintf(
                    'The service "%s" will become a doctrine repository, so it cannot be arguments!',
                    $serviceId
                ));
            }

            if (!isset($repoConfig['entity'])) {
                throw new \LogicException(
                    'It is obligatory to pass the mapping name of an entity which should get this repository!'
                );
            } else {
                $entity = $repoConfig['entity'];
            }

            $definition->setFactory([new Reference($entityManagerServiceId), 'getRepository']);
            $definition->addArgument($entity);
        }
    }
}
