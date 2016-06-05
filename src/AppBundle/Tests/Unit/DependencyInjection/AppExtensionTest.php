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

namespace AppBundle\Tests\Unit\DependencyInjection;

use AppBundle\DependencyInjection\AppExtension;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadExtension()
    {
        $container = new ContainerBuilder();
        $extension = new AppExtension();

        $extension->load([], $container);
        $resources = $container->getResources();
        $this->assertCount(6, $resources);

        $names = array_map(function (FileResource $resource) {
            $split = explode('/', $resource->getResource());

            return end($split);
        }, $resources);

        $this->assertCount(
            0,
            array_diff(
                ['listeners.yml', 'commands.yml', 'redis.yml', 'repositories.yml', 'services.yml', 'validators.yml'],
                $names
            )
        );
    }
}
