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

namespace AppBundle\Tests\Unit\DependencyInjection\Compiler;

use AppBundle\DependencyInjection\Compiler\ConnectChannelsWithDelegatorPass;
use AppBundle\Service\Notification\Channel\MailingChannel;
use AppBundle\Service\Notification\ChannelDelegatingNotificator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConnectChannelsWithDelegatorPassTest extends \PHPUnit_Framework_TestCase
{
    public function testNotificatorNotRegisteredInContainer(): void
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ConnectChannelsWithDelegatorPass());

        $definition = new Definition(MailingChannel::class);
        $definition->setArguments([[], []]);
        $definition->addTag('app.notificator.channel', []);

        $container->setDefinition('test_channel', $definition);
        $container->compile();

        self::assertTrue($container->hasDefinition('test_channel'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The tag `app.notificator.channel` can be declared one time only!
     */
    public function testTwiceDeclaredTag(): void
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ConnectChannelsWithDelegatorPass());

        $definition = new Definition(MailingChannel::class);
        $definition->setArguments([[], []]);
        $definition->addTag('app.notificator.channel', []);
        $definition->addTag('app.notificator.channel', []);

        $container->setDefinition('test_channel', $definition);
        $container->setDefinition('app.notification', new Definition(ChannelDelegatingNotificator::class));

        $container->compile();
    }

    public function testConnectNotificationChannelWithServiceDefinition(): void
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ConnectChannelsWithDelegatorPass());

        $definition = new Definition(MailingChannel::class);
        $definition->setArguments([[], []]);
        $definition->addTag('app.notificator.channel', ['alias' => 'mail']);

        $container->setDefinition('test_channel', $definition);
        $container->setDefinition('app.notification', new Definition(ChannelDelegatingNotificator::class, [[], []]));

        $container->compile();

        $arg2 = $container->getDefinition('app.notification')->getArgument(1);
        self::assertCount(1, $arg2);
        self::assertArrayHasKey('mail', $arg2);
        self::assertSame((string) $arg2['mail'], 'test_channel');
        self::assertInstanceOf(Reference::class, $arg2['mail']);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The attribute "alias" is missing in the tag defintion "app.notificator.channel" of service "test_channel"!
     */
    public function testMissingAliasAttributeInTagDefinition(): void
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new ConnectChannelsWithDelegatorPass());

        $definition = new Definition(MailingChannel::class);
        $definition->setArguments([[], []]);
        $definition->addTag('app.notificator.channel');

        $container->setDefinition('test_channel', $definition);
        $container->setDefinition('app.notification', new Definition(ChannelDelegatingNotificator::class, [[], []]));

        $container->compile();
    }
}
