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

use AppBundle\DependencyInjection\Compiler\AddSuggestorToNameSuggestionChainPass;
use AppBundle\Model\User\Util\NameSuggestion\ChainSuggestor;
use AppBundle\Model\User\Util\NameSuggestion\Suggestor\YearPostfixSuggestor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddSuggestorToNameSuggestionChainPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The tag `app.registration.suggestor` must be declared one time only!
     */
    public function testMultipleTagDeclarations()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AddSuggestorToNameSuggestionChainPass());

        $container->setDefinition('app.user.registration.name_suggestor', new Definition(ChainSuggestor::class));
        $suggestor = new Definition(YearPostfixSuggestor::class);
        $suggestor->addTag('app.registration.suggestor', []);
        $suggestor->addTag('app.registration.suggestor', []);

        $container->setDefinition('suggestor', $suggestor);
        $container->compile();
    }

    public function testAddSuggestorToChain()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AddSuggestorToNameSuggestionChainPass());

        $container->setDefinition('app.user.registration.name_suggestor', new Definition(ChainSuggestor::class));
        $suggestor = new Definition(YearPostfixSuggestor::class);
        $suggestor->addTag('app.registration.suggestor', []);

        $container->setDefinition('suggestor', $suggestor);
        $container->compile();

        $chain = $container->getDefinition('app.user.registration.name_suggestor');
        self::assertCount(1, $chain->getMethodCalls());

        $call = $chain->getMethodCalls()[0];
        self::assertSame('register', $call[0]);
        self::assertCount(1, $call[1]);
        self::assertInstanceOf(Reference::class, $call[1][0]);
        self::assertSame('suggestor', (string) $call[1][0]);
    }
}
