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

namespace AppBundle\Tests\Unit\DependencyInjection\Compiler;

use AppBundle\DependencyInjection\Compiler\ConfigureNotificatableCommandHandlersPass;
use AppBundle\Model\User\Handler\CreateUserHandler;
use AppBundle\Service\Notification\ChannelDelegatingNotificator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConfigureNotificatableCommandHandlersPassTest extends \PHPUnit_Framework_TestCase
{
    public function testMissingDefinition()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ConfigureNotificatableCommandHandlersPass());

        $definition = new Definition(CreateUserHandler::class); // we use the user handler for testing purposes here
        $definition->addTag(
            'app.command_handler.notificatable',
            ['template' => 'AppBundle:Email/Activation:activation']
        );

        $container->setDefinition('app.handler.create_user', $definition);
        $container->compile();

        $this->assertTrue($container->hasDefinition('app.handler.create_user'));
        $this->assertCount(0, $container->getDefinition('app.handler.create_user')->getMethodCalls());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The tag `app.command_handler.notificatable` must be declared one time only!
     */
    public function testDeclareTagMultipleTimes()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ConfigureNotificatableCommandHandlersPass());

        $definition = new Definition(CreateUserHandler::class); // we use the user handler for testing purposes here
        $definition->addTag(
            'app.command_handler.notificatable',
            ['template' => 'AppBundle:Email/Activation:activation']
        );
        $definition->addTag(
            'app.command_handler.notificatable',
            ['template' => 'AppBundle:Email/Activation:activation']
        );

        $container->setDefinition('app.handler.create_user', $definition);
        $container->setDefinition('app.notification', new Definition(ChannelDelegatingNotificator::class));
        $container->compile();
    }

    public function testConnectNotificatorServiceWithHandler()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ConfigureNotificatableCommandHandlersPass());

        $definition = new Definition(CreateUserHandler::class); // we use the user handler for testing purposes here
        $definition->addTag(
            'app.command_handler.notificatable',
            ['template' => 'AppBundle:Email/Activation:activation']
        );

        $container->setDefinition('app.handler.create_user', $definition);
        $container->setDefinition('app.notification', new Definition(
            ChannelDelegatingNotificator::class,
            [[], []]
        ));
        $container->compile();

        $this->assertTrue($container->hasDefinition('app.handler.create_user'));

        $definition = $container->getDefinition('app.handler.create_user');
        $this->assertCount(1, $calls = $definition->getMethodCalls());

        $first = $calls[0];
        $this->assertSame('setNotificator', $first[0]);
        $this->assertSame('app.notification', (string) $first[1][0]);

        $arg = $container->getDefinition('app.notification')->getArgument(0);
        $this->assertArrayHasKey(CreateUserHandler::class, $arg);
        $this->assertSame('AppBundle:Email/Activation:activation', $arg[CreateUserHandler::class]);
    }

    public function testNoTemplateIsPresentAsTagAttribute()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ConfigureNotificatableCommandHandlersPass());

        $definition = new Definition(CreateUserHandler::class); // we use the user handler for testing purposes here
        $definition->addTag(
            'app.command_handler.notificatable',
            []
        );

        $container->setDefinition('app.handler.create_user', $definition);
        $container->setDefinition('app.notification', new Definition(
            ChannelDelegatingNotificator::class,
            [[], []]
        ));
        $container->compile();

        self::assertCount(1, $calls = $container->getDefinition('app.handler.create_user')->getMethodCalls());
        self::assertCount(0, $container->getDefinition('app.notification')->getArgument(1));
        self::assertSame((string) $calls[0][0], 'setNotificator');
    }
}
