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

namespace AppBundle\Tests\DependencyInjection\Compiler;

use AppBundle\DependencyInjection\Compiler\RegisterDoctrineRepositoriesPass;
use AppBundle\Model\User\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterDoctrineRepositoriesPassTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterRepository()
    {
        $compilerPass      = new RegisterDoctrineRepositoriesPass();
        $containerBuilder  = new ContainerBuilder();
        $managerDefinition = new Definition(EntityManager::class);

        $containerBuilder->setDefinition('doctrine.orm.default_entity_manager', $managerDefinition);

        $repositoryDefinition = new Definition(UserRepository::class);
        $repositoryDefinition->addTag('app.tag.repository', ['entity' => 'Account:User']);

        $containerBuilder->setDefinition('user_repo', $repositoryDefinition);

        $compilerPass->process($containerBuilder);

        $processedDefinition = $containerBuilder->getDefinition('user_repo');
        $this->assertNotNull($processedDefinition);

        list($reference, $method) = $processedDefinition->getFactory();
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertSame((string) $reference, 'doctrine.orm.default_entity_manager');
        $this->assertSame($method, 'getRepository');

        $argument = $processedDefinition->getArgument(0);
        $this->assertSame('Account:User', $argument);

        $this->assertCount(1, $processedDefinition->getArguments());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The service "user_repo" cannot be tagged with "app.tag.repository" more than one time!
     */
    public function testMultipleTags()
    {
        $compilerPass     = new RegisterDoctrineRepositoriesPass();
        $containerBuilder = new ContainerBuilder();

        $repositoryDefinition = new Definition(UserRepository::class);
        $repositoryDefinition->addTag('app.tag.repository', ['entity' => 'Account:User']);
        $repositoryDefinition->addTag('app.tag.repository', ['entity' => 'Account:Role']);

        $containerBuilder->setDefinition('user_repo', $repositoryDefinition);
        $compilerPass->process($containerBuilder);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The given entity manager service id "doctrine.orm.default_entity_manager" does not exist in the container!
     */
    public function testInvalidEntityManager()
    {
        $compilerPass     = new RegisterDoctrineRepositoriesPass();
        $containerBuilder = new ContainerBuilder();

        $repositoryDefinition = new Definition(UserRepository::class);
        $repositoryDefinition->addTag('app.tag.repository', ['entity' => 'Account:User']);

        $containerBuilder->setDefinition('user_repo', $repositoryDefinition);

        $compilerPass->process($containerBuilder);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The service "user_repo" will become a doctrine repository, so it cannot be arguments!
     */
    public function testArgumentsGiven()
    {
        $compilerPass      = new RegisterDoctrineRepositoriesPass();
        $containerBuilder  = new ContainerBuilder();
        $managerDefinition = new Definition(EntityManager::class);

        $containerBuilder->setDefinition('doctrine.orm.default_entity_manager', $managerDefinition);

        $repositoryDefinition = new Definition(UserRepository::class);
        $repositoryDefinition->addTag('app.tag.repository', ['entity' => 'Account:User']);
        $repositoryDefinition->addArgument('Account:User');

        $containerBuilder->setDefinition('user_repo', $repositoryDefinition);

        $compilerPass->process($containerBuilder);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage It is obligatory to pass the mapping name of an entity which should get this repository!
     */
    public function testMissingAttributes()
    {
        $compilerPass      = new RegisterDoctrineRepositoriesPass();
        $containerBuilder  = new ContainerBuilder();
        $managerDefinition = new Definition(EntityManager::class);

        $containerBuilder->setDefinition('doctrine.orm.default_entity_manager', $managerDefinition);

        $repositoryDefinition = new Definition(UserRepository::class);
        $repositoryDefinition->addTag('app.tag.repository');

        $containerBuilder->setDefinition('user_repo', $repositoryDefinition);

        $compilerPass->process($containerBuilder);
    }
}
