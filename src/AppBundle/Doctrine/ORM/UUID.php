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

namespace AppBundle\Doctrine\ORM;

use AppBundle\Doctrine\ORM\Id\UUIDInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\UuidGenerator;

/**
 * Utility to generate universal unique identifiers for domain models.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class UUID implements UUIDInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \LogicException If the entity manager instance cannot be used.
     */
    public function generateUUIDForEntity(EntityManagerInterface $entityManager, $entity)
    {
        if (!$entityManager instanceof EntityManager) {
            throw new \LogicException(sprintf(
                'Currently the given entity manager must be an instance of "%s". This issue will be fixed in doctrine 3.x. Please see doctrine/doctrine2#5549 (https://github.com/doctrine/doctrine2/pull/5549) for more information!',
                EntityManager::class
            ));
        }

        static $instance = null;
        if (!$instance) {
            $instance = new UuidGenerator();
        }

        return $instance->generate($entityManager, $entity);
    }
}
