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

namespace AppBundle\Behat\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class EmptyEntityManager implements EntityManagerInterface
{
    public function getCache()
    {
    }

    public function getConnection()
    {
    }

    public function getExpressionBuilder()
    {
    }

    public function beginTransaction()
    {
    }

    public function transactional($func)
    {
    }

    public function commit()
    {
    }

    public function rollback()
    {
    }

    public function createQuery($dql = '')
    {
    }

    public function createNamedQuery($name)
    {
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
    }

    public function createNamedNativeQuery($name)
    {
    }

    public function createQueryBuilder()
    {
    }

    public function getReference($entityName, $id)
    {
    }

    public function getPartialReference($entityName, $identifier)
    {
    }

    public function close()
    {
    }

    public function copy($entity, $deep = false)
    {
    }

    public function lock($entity, $lockMode, $lockVersion = null)
    {
    }

    public function getEventManager()
    {
    }

    public function getConfiguration()
    {
    }

    public function isOpen()
    {
    }

    public function getUnitOfWork()
    {
    }

    public function getHydrator($hydrationMode)
    {
    }

    public function newHydrator($hydrationMode)
    {
    }

    public function getProxyFactory()
    {
    }

    public function getFilters()
    {
    }

    public function isFiltersStateClean()
    {
    }

    public function hasFilters()
    {
    }

    public function find($className, $id)
    {
    }

    public function persist($object)
    {
    }

    public function remove($object)
    {
    }

    public function merge($object)
    {
    }

    public function clear($objectName = null)
    {
    }

    public function detach($object)
    {
    }

    public function refresh($object)
    {
    }

    public function flush()
    {
    }

    public function getRepository($className)
    {
    }

    public function getClassMetadata($className)
    {
    }

    public function getMetadataFactory()
    {
    }

    public function initializeObject($obj)
    {
    }

    public function contains($object)
    {
    }
}
