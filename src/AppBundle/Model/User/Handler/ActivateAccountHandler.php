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

namespace AppBundle\Model\User\Handler;

use AppBundle\Exception\UserActivationException;
use AppBundle\Model\User\DTO\ActivateAccountDTO;
use AppBundle\Model\User\RoleReadRepositoryInterface;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserReadRepositoryInterface;
use AppBundle\Model\User\UserWriteRepositoryInterface;

/**
 * Handle which is responsible for account activation.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class ActivateAccountHandler
{
    /**
     * @var UserReadRepositoryInterface
     */
    private $userReadRepository;

    /**
     * @var UserWriteRepositoryInterface
     */
    private $userWriteRepository;

    /**
     * @var RoleReadRepositoryInterface
     */
    private $roleRepository;

    /**
     * Constructor.
     *
     * @param UserReadRepositoryInterface  $userReadRepository
     * @param UserWriteRepositoryInterface $userWriteRepository
     * @param RoleReadRepositoryInterface  $roleRepository
     */
    public function __construct(
        UserReadRepositoryInterface $userReadRepository,
        UserWriteRepositoryInterface $userWriteRepository,
        RoleReadRepositoryInterface $roleRepository
    ) {
        $this->userReadRepository  = $userReadRepository;
        $this->userWriteRepository = $userWriteRepository;
        $this->roleRepository      = $roleRepository;
    }

    /**
     * Activates the account.
     *
     * @param ActivateAccountDTO $activateAccountDTO
     */
    public function __invoke(ActivateAccountDTO $activateAccountDTO): void
    {
        if (!$user = $this->userReadRepository->findUserByUsernameAndActivationKey($activateAccountDTO->username, $activateAccountDTO->activationKey)) {
            throw new UserActivationException();
        }

        if ($user->getPendingActivation()->isActivationExpired()) {
            $this->userWriteRepository->remove($user);

            throw new UserActivationException();
        }

        $user->performStateTransition(User::STATE_APPROVED, $activateAccountDTO->activationKey);

        // the role needs to be added during approval since a non-approved user must not have any role in the system.
        // Furthermore it leads to technical issues when running the purger as the roles may cause a constraint violation
        // in the RDBMS. Therefore it's safer to add roles during the approval.
        $user->addRole($this->roleRepository->determineDefaultRole());

        $this->userWriteRepository->save($user);
    }
}
