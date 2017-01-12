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

namespace AppBundle\Tests\Unit\DependencyInjection;

use AppBundle\DependencyInjection\AppExtension;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadExtension(): void
    {
        $container = new ContainerBuilder();
        $extension = new AppExtension();

        $extension->load([], $container);
        $resources = $container->getResources();
        $this->assertCount(9, $resources);

        $names = array_map(function (FileResource $resource) {
            $split = explode('/', $resource->getResource());

            return end($split);
        }, $resources);

        $this->assertCount(
            0,
            array_diff(
                ['listeners.yml', 'commands.yml', 'redis.yml', 'repositories.yml', 'services.yml', 'validators.yml', 'suggestors.yml'],
                $names
            )
        );
    }
}
