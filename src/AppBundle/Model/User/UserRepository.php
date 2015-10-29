<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User;

use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Repository that contains custom dql calls for the user model.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UserRepository extends EntityRepository
{
    /**
     * Deletes all activations that were pending and are in a given time period.
     *
     * @param DateTime $dateTime
     *
     * @throws DBALException If something went wrong with the transaction
     *
     * @return int
     */
    public function deletePendingActivationsByDate(DateTime $dateTime)
    {
        $connection = $this->_em->getConnection();
        try {
            // we have to wrap all the deletion logic into a transaction
            // although the deletion statement will be executed in a new nested transaction.
            // This is because all tables must be locked in order to prevent users from activating
            // their accounts when the purger has already started.
            // The activation must be done before that.
            $connection->beginTransaction();

            $query = $this->queryUserIdsWithPendingActivation($dateTime);
            $query->setHydrationMode(Query::HYDRATE_ARRAY);
            $paginator = new Paginator($query);

            $qb    = $this->_em->createQueryBuilder();
            $count = count($paginator); // paginator count must be executed before the deletion statement

            $qb
                ->delete('Account:User', 'user')
                ->where($qb->expr()->in('user.id', ':ids'))
                ->setParameter(':ids', iterator_to_array($paginator));

            $qb->getQuery()->execute();

            $connection->commit();

            return $count;
        } catch (DBALException $ex) {
            $connection->rollBack();

            throw $ex;
        }
    }

    /**
     * Creates a list of old entity ids that should be removed.
     *
     * @param DateTime $dateTime
     *
     * @return Query
     */
    private function queryUserIdsWithPendingActivation(DateTime $dateTime)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('partial user.{id}')
            ->distinct()
            ->from('Account:User', 'user')
            ->join('user.pendingActivation', 'pending_activation')
            ->where($qb->expr()->lt('pending_activation.activationDate', ':date_time'))
            ->andWhere($qb->expr()->eq('user.state', ':state'))
            ->setParameter(':date_time', $dateTime)
            ->setParameter(':state', User::STATE_NEW);

        return $qb->getQuery();
    }
}
