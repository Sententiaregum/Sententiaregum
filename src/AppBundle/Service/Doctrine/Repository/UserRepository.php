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

namespace AppBundle\Service\Doctrine\Repository;

use AppBundle\Model\User\User;
use AppBundle\Model\User\UserReadRepositoryInterface;
use AppBundle\Model\User\UserWriteRepositoryInterface;
use DateTime;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Repository that contains custom dql calls for the user model.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UserRepository extends EntityRepository implements UserReadRepositoryInterface, UserWriteRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception If something went wrong with the transaction
     */
    public function deletePendingActivationsByDate(DateTime $dateTime): int
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
        } catch (\Exception $ex) {
            $connection->rollBack();

            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception If something in the DB transaction went wrong.
     */
    public function deleteAncientAttemptData(DateTime $dateTime): int
    {
        $connection = $this->_em->getConnection();

        // build a transaction on top of the rest to avoid side-effects
        try {
            $connection->beginTransaction();

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

            if (!empty($ids)) {
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

            $connection->commit();

            return 0;
        } catch (\Exception $ex) {
            $connection->rollBack();

            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFollowingIdsByUser(User $user): array
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
     * {@inheritdoc}
     */
    public function findUserByUsernameAndActivationKey(string $username, string $activationKey)
    {
        return $this->findOneBy(['username' => $username, 'pendingActivation.key' => $activationKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function filterUniqueUsernames(array $names): array
    {
        $qb        = $this->_em->createQueryBuilder();
        $nonUnique = array_column(
            $qb
                ->select('user.username')
                ->from('Account:User', 'user')
                ->where($qb->expr()->in('user.username', ':names'))
                ->setParameter(':names', $names)
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY),
            'username'
        );

        return array_values(// re-index array after filter process
            array_filter(
                $names,
                function ($username) use ($nonUnique) {
                    return !in_array($username, $nonUnique, true);
                }
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function save(User $user): string
    {
        $this->_em->persist($user);

        return $user->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(User $user)
    {
        $this->_em->remove($user);
    }

    /**
     * Creates a list of old entity ids that should be removed.
     *
     * @param DateTime $dateTime
     *
     * @return Query
     */
    private function buildQueryForUserIdsWithOldPendingActivations(DateTime $dateTime): Query
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('partial user.{id}')
            ->distinct()
            ->from('Account:User', 'user')
            ->where($qb->expr()->lt('user.pendingActivation.activationDate', ':date_time'))
            ->setParameter(':date_time', $dateTime, Type::DATETIME);

        return $qb->getQuery();
    }
}
