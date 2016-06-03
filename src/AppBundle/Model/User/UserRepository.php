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

namespace AppBundle\Model\User;

use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

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

            $query = $this->buildQueryForUserIdsWithOldPendingActivations($dateTime);
            $query->setHydrationMode(Query::HYDRATE_ARRAY);

            $qb       = $this->_em->createQueryBuilder();
            $affected = 0;

            if ($ids = $query->getResult()) {
                $qb
                    ->delete('Account:User', 'user')
                    ->where($qb->expr()->in('user.id', ':ids'))
                    ->setParameter(':ids', $ids);

                $affected = $qb->getQuery()->execute();
            }

            $connection->commit();

            return $affected;
        } catch (DBALException $ex) {
            $connection->rollBack();

            throw $ex;
        }
    }

    /**
     * Deletes all attempt models containing failed attempts which are too old.
     *
     * @param DateTime $dateTime
     *
     * @return int
     */
    public function deleteAncientAttemptData(DateTime $dateTime)
    {
        $qb     = $this->_em->createQueryBuilder();
        $search = clone $qb;
        $expr   = $qb->expr();

        // unfortunately DQL can't do joins on DELETE queries
        $idQuery = $search
            ->select('authentication_attempt.id')
            ->distinct()
            ->from('Account:AuthenticationAttempt', 'authentication_attempt')
            ->join('Account:User', 'user', Join::WITH, $expr->isMemberOf(
                'authentication_attempt',
                'user.failedAuthentications'
            ))
            ->where($expr->lt(
                'authentication_attempt.latestDateTime',
                ':date_time'
            ))
            ->setParameter(':date_time', $dateTime, Type::DATETIME)
            ->getQuery()
            ->setHydrationMode(Query::HYDRATE_ARRAY);

        $ids = array_column($idQuery->getResult(), 'id');

        if ($ids) {
            $connection    = $this->_em->getConnection();
            $list          = implode(',', array_fill(0, count($ids), '?'));
            $relationQuery = $connection->prepare("DELETE FROM `FailedAuthAttempt2User` WHERE `attemptId` IN ({$list})");

            // drop relations manually before removing the models
            $result = $relationQuery->execute($ids);

            if ($result) {
                $affected = $qb
                    ->delete('Account:AuthenticationAttempt', 'attempt')
                    ->where($expr->in('attempt.id', ':ids'))
                    ->setParameter(':ids', $ids)
                    ->getQuery()
                    ->execute();

                return $affected + $result;
            }
        }

        return 0;
    }

    /**
     * Creates a list that contains the ids of all users following a specific user.
     *
     * @param User $user
     *
     * @return int[]
     */
    public function getFollowingIdsByUser(User $user)
    {
        $qb = $this->_em->createQueryBuilder();

        $result = $qb
            ->select('partial user.{id}')
            ->distinct()
            ->from('Account:User', 'user')
            ->join('Account:User', 'current_user', Join::WITH, $qb->expr()->eq('current_user.id', ':user_id'))
            ->where($qb->expr()->isMemberOf('user', 'current_user.following'))
            ->setParameter(':user_id', $user->getId())
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return array_column($result, 'id');
    }

    /**
     * Loads a user by its username and activation key.
     *
     * @param string $username
     * @param string $activationKey
     *
     * @return User
     */
    public function findUserByUsernameAndActivationKey($username, $activationKey)
    {
        return $this->findOneBy(['username' => $username, 'activationKey' => $activationKey]);
    }

    /**
     * Creates a list of old entity ids that should be removed.
     *
     * @param DateTime $dateTime
     *
     * @return Query
     */
    private function buildQueryForUserIdsWithOldPendingActivations(DateTime $dateTime)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('partial user.{id}')
            ->distinct()
            ->from('Account:User', 'user')
            ->join('user.pendingActivation', 'pending_activation')
            ->where($qb->expr()->lt('pending_activation.activationDate', ':date_time'))
            ->setParameter(':date_time', $dateTime, Type::DATETIME);

        return $qb->getQuery();
    }
}
