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

namespace AppBundle\Service\Doctrine\DataFixtures;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Fixture loader which automates the whole loading process.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class ConfigurableFixturesLoader
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Loads fixtures that are marked as production fixtures.
     *
     * @param string $directory
     *
     * @return ProductionFixtureInterface[]
     */
    public function loadProductionFixturesFromDirectory(string $directory): array
    {
        $loader = new Loader();

        return array_filter(
            $loader->loadFromDirectory($directory),
            function ($fixture) {
                return $fixture instanceof ProductionFixtureInterface;
            }
        );
    }

    /**
     * Loads the data fixtures passed as array.
     *
     * @param array    $fixtureClasses
     * @param callable $executorLogFunction
     *
     * @throws \InvalidArgumentException If the fixture class does not exist.
     */
    public function applyFixtures(array $fixtureClasses, callable $executorLogFunction = null): void
    {
        $loader = new Loader();
        foreach ($fixtureClasses as $fixtureClass) {
            if (!class_exists($fixtureClass)) {
                throw new \InvalidArgumentException(sprintf('Fixture class "%s" does not exist!', $fixtureClass));
            }

            $loader->addFixture(new $fixtureClass());
        }

        $purger   = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);

        if (null !== $executorLogFunction) {
            $executor->setLogger($executorLogFunction);
        }

        $executor->execute($loader->getFixtures());
    }
}
