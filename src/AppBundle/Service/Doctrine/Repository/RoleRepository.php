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

namespace AppBundle\Service\Doctrine\Repository;

use AppBundle\Model\User\Role;
use AppBundle\Model\User\RoleReadRepositoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Repository class for the role entity.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class RoleRepository extends EntityRepository implements RoleReadRepositoryInterface
{
    const DEFAULT_USER_ROLE = 'ROLE_USER';

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If the default role is not present.
     */
    public function determineDefaultRole(): Role
    {
        $defaultRole = $this->findOneBy(['role' => self::DEFAULT_USER_ROLE]);
        if (!$defaultRole) {
            throw new \RuntimeException(sprintf(
                'Role "%s" is not present!',
                self::DEFAULT_USER_ROLE
            ));
        }

        return $defaultRole;
    }
}
