<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Doctrine\ORM;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Fixture loader which automates the whole loading process.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service("app.doctrine.fixtures_loader")
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
     *
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Loads the data fixtures passed as array.
     *
     * @param array $fixtureClasses
     *
     * @throws \InvalidArgumentException If the fixture class does not exist.
     */
    public function loadFixtures(array $fixtureClasses)
    {
        $loader = new Loader();
        foreach ($fixtureClasses as $fixtureClass) {
            if ($fixtureClass instanceof FixtureInterface) {
                $loader->addFixture($fixtureClass);
            } else {
                if (!class_exists($fixtureClass)) {
                    throw new \InvalidArgumentException(sprintf('Fixture class "%s" does not exist!', $fixtureClass));
                }

                $loader->addFixture(new $fixtureClass);
            }
        }

        $purger   = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);

        $executor->execute($loader->getFixtures());
    }
}
