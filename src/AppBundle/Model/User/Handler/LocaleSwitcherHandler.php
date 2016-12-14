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

use AppBundle\Model\User\DTO\LocaleSwitcherDTO;
use AppBundle\Model\User\UserWriteRepositoryInterface;

/**
 * Handler which changes the locale of the logged in user.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class LocaleSwitcherHandler
{
    /**
     * @var UserWriteRepositoryInterface
     */
    private $userRepository;

    /**
     * Constructor.
     *
     * @param UserWriteRepositoryInterface $userRepository
     */
    public function __construct(UserWriteRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Modifies the user locale.
     *
     * @param LocaleSwitcherDTO $dto
     */
    public function __invoke(LocaleSwitcherDTO $dto): void
    {
        $user = $dto->user;
        if ($dto->locale !== $user->getLocale()) {
            $user->modifyUserLocale($dto->locale);

            $this->userRepository->save($user);
        }
    }
}
