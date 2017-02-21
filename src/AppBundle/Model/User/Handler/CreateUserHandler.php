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

namespace AppBundle\Model\User\Handler;

use AppBundle\Model\Core\Util\NotificatableTrait;
use AppBundle\Model\User\DTO\CreateUserDTO;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserWriteRepositoryInterface;
use AppBundle\Model\User\Util\ActivationKeyCode\ActivationKeyCodeGeneratorInterface;
use AppBundle\Validator\Constraints\UniqueProperty;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handler for the user creation.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
final class CreateUserHandler
{
    use NotificatableTrait;

    /**
     * @var UserWriteRepositoryInterface
     */
    private $userRepository;

    /**
     * @var PasswordHasherInterface
     */
    private $hasher;

    /**
     * @var ActivationKeyCodeGeneratorInterface
     */
    private $keyGenerator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param UserWriteRepositoryInterface        $userRepository
     * @param PasswordHasherInterface             $passwordHasher
     * @param ActivationKeyCodeGeneratorInterface $keyGenerator
     * @param ValidatorInterface                  $validator
     */
    public function __construct(
        UserWriteRepositoryInterface $userRepository,
        PasswordHasherInterface $passwordHasher,
        ActivationKeyCodeGeneratorInterface $keyGenerator,
        ValidatorInterface $validator
    ) {
        $this->userRepository  = $userRepository;
        $this->hasher          = $passwordHasher;
        $this->keyGenerator    = $keyGenerator;
        $this->validator       = $validator;
    }

    /**
     * Handles a the user creation.
     *
     * @param CreateUserDTO $userDTO
     *
     * @throws \OverflowException If the activation keycode generation failed.
     * @throws \LogicException
     */
    public function __invoke(CreateUserDTO $userDTO): void
    {
        $user = User::create($userDTO->username, $userDTO->password, $userDTO->email, $this->hasher);
        $user->modifyUserLocale($userDTO->locale);

        $rounds        = 0;
        $isUnique      = false;
        $activationKey = null;
        while (!$isUnique) {
            ++$rounds;

            if ($rounds >= 200) {
                throw new \OverflowException('Cannot generate activation key!');
            }

            $activationKey = $this->keyGenerator->generate(255);
            $options       = [
                'entity' => 'Account:User',
                'field'  => 'pendingActivation.key',
            ];

            $isUnique = count($this->validator->validate($activationKey, new UniqueProperty($options))) === 0;
        }

        $user->storeUniqueActivationKeyForNonApprovedUser($activationKey);
        $this->userRepository->save($user);

        $this->notify(
            [
                'activation_key' => $user->getPendingActivation()->getKey(),
                'username'       => $user->getUsername(),
            ],
            [$user],
            ['mail'],
            $user->getLocale()
        );

        $userDTO->user = $user;
    }
}
