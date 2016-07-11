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

use AppBundle\Event\MailerEvent;
use AppBundle\Model\User\DTO\CreateUserDTO;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserWriteRepositoryInterface;
use AppBundle\Model\User\Util\ActivationKeyCode\ActivationKeyCodeGeneratorInterface;
use AppBundle\Validator\Constraints\UniqueProperty;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PasswordHasherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handler for the user creation.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class CreateUserHandler
{
    /**
     * @var UserWriteRepositoryInterface
     */
    private $userRepository;

    /**
     * @var PasswordHasherInterface
     */
    private $hasher;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

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
     * @param EventDispatcherInterface            $dispatcher
     * @param ActivationKeyCodeGeneratorInterface $keyGenerator
     * @param ValidatorInterface                  $validator
     */
    public function __construct(
        UserWriteRepositoryInterface $userRepository,
        PasswordHasherInterface $passwordHasher,
        EventDispatcherInterface $dispatcher,
        ActivationKeyCodeGeneratorInterface $keyGenerator,
        ValidatorInterface $validator
    ) {
        $this->eventDispatcher = $dispatcher;
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
     */
    public function __invoke(CreateUserDTO $userDTO)
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
            $options = [
                'entity' => 'Account:User',
                'field'  => 'pendingActivation.key',
            ];

            $isUnique = count($this->validator->validate($activationKey, new UniqueProperty($options))) === 0;
        }

        $user->storeUniqueActivationKeyForNonApprovedUser($activationKey);
        $this->userRepository->save($user);

        $mailerEvent = new MailerEvent();
        $mailerEvent
            ->setTemplateSource('AppBundle:Email/Activation:activation')
            ->addUser($user)
            ->addParameter('activation_key', $user->getPendingActivation()->getKey())
            ->addParameter('username', $user->getUsername())
            ->setLanguage($user->getLocale());

        $this->eventDispatcher->dispatch(MailerEvent::EVENT_NAME, $mailerEvent);

        $userDTO->user = $user;
    }
}
