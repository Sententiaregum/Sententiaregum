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
use AppBundle\Exception\UserActivationException;
use AppBundle\Model\User\DTO\CreateUserDTO;
use AppBundle\Model\User\RoleReadRepositoryInterface;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserReadRepositoryInterface;
use AppBundle\Model\User\Util\ActivationKeyCode\ActivationKeyCodeGeneratorInterface;
use AppBundle\Model\User\Util\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Model\User\Value\RegistrationResult;
use AppBundle\Validator\Constraints\UniqueProperty;
use Doctrine\ORM\EntityManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PasswordHasherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Concrete implementation of the registration approach.
 *
 * This process is split in two steps:
 *  Step 1: a simple user will be stored with the state "STATE_NEW".
 *          The user receives a notification with an activation key.
 *  Step 2: after entering the activation key, the user state will be switched to "STATE_APPROVED".
 *          Now the user is able to login.
 *
 * These two steps are necessary in order to prevent spam bots from creating
 * an account as the activation must be processed when having a valid email account.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class TwoStepRegistrationApproach
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ActivationKeyCodeGeneratorInterface
     */
    private $activationKeyCodeGenerator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var PasswordHasherInterface
     */
    private $hasher;

    /**
     * @var SuggestorInterface
     */
    private $suggestor;

    /**
     * @var UserReadRepositoryInterface
     */
    private $userRepository;

    /**
     * @var RoleReadRepositoryInterface
     */
    private $roleRepository;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface              $entityManager
     * @param ActivationKeyCodeGeneratorInterface $activationKeyCodeGenerator
     * @param ValidatorInterface                  $validator
     * @param EventDispatcherInterface            $eventDispatcher
     * @param PasswordHasherInterface             $passwordHasher
     * @param SuggestorInterface                  $nameSuggestor
     * @param UserReadRepositoryInterface         $userRepository
     * @param RoleReadRepositoryInterface         $roleRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ActivationKeyCodeGeneratorInterface $activationKeyCodeGenerator,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher,
        PasswordHasherInterface $passwordHasher,
        SuggestorInterface $nameSuggestor,
        UserReadRepositoryInterface $userRepository,
        RoleReadRepositoryInterface $roleRepository
    ) {
        $this->entityManager              = $entityManager;
        $this->activationKeyCodeGenerator = $activationKeyCodeGenerator;
        $this->validator                  = $validator;
        $this->eventDispatcher            = $eventDispatcher;
        $this->hasher                     = $passwordHasher;
        $this->suggestor                  = $nameSuggestor;
        $this->userRepository             = $userRepository;
        $this->roleRepository             = $roleRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UserActivationException If the activation fails
     * @throws \RuntimeException       If the role is not present
     */
    public function approveByActivationKey(string $activationKey, string $username)
    {
        $user = $this->findUserByActivationKeyAndUsername($activationKey, $username);
        $user->modifyActivationStatus(User::STATE_APPROVED, $activationKey);

        $user->addRole($this->roleRepository->determineDefaultRole());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function registration(CreateUserDTO $userParameters): RegistrationResult
    {
        $result = $this->buildAndValidateUserModelByDTO($userParameters);
        if (!$result['valid']) {
            return new RegistrationResult(
                $result['violations'],
                $this->generateUsernameSuggestionsByNonUniqueUsername(
                    $result['violations'],
                    $userParameters->getUsername()
                )
            );
        }

        $user = $result['user'];

        $this->entityManager->persist($user);

        $this->sendActivationEmail($user);
        $this->entityManager->flush();

        return new RegistrationResult(null, [], $user);
    }

    /**
     * Builds a new user and validates it.
     *
     * @param \AppBundle\Model\User\DTO\CreateUserDTO $userParameters
     *
     * @return mixed[]
     */
    private function buildAndValidateUserModelByDTO(CreateUserDTO $userParameters): array
    {
        $violations = $this->validator->validate($userParameters);
        if (count($violations) > 0) {
            return [
                'valid'      => false,
                'violations' => $violations,
            ];
        }

        $newUser = User::create(
            $userParameters->getUsername(),
            $userParameters->getPassword(),
            $userParameters->getEmail(),
            $this->hasher
        );

        $newUser->modifyUserLocale($userParameters->getLocale());
        $this->buildNotActivatedUser($newUser);

        return [
            'valid' => true,
            'user'  => $newUser,
        ];
    }

    /**
     * Sends the activation email.
     *
     * @param User $persistentUser
     */
    private function sendActivationEmail(User $persistentUser)
    {
        $mailerEvent = new MailerEvent();
        $mailerEvent
            ->setTemplateSource('AppBundle:Email/Activation:activation')
            ->addUser($persistentUser)
            ->addParameter('activation_key', $persistentUser->getPendingActivation()->getKey())
            ->addParameter('username', $persistentUser->getUsername())
            ->setLanguage($persistentUser->getLocale());

        $this->eventDispatcher->dispatch(MailerEvent::EVENT_NAME, $mailerEvent);
    }

    /**
     * Generates an unique activation key.
     *
     * @return string
     */
    private function getUniqueActivationKey(): string
    {
        $rounds = 0;

        do {
            ++$rounds;

            if ($this->tooManyGenerationAttempts($rounds)) {
                throw new \OverflowException('Cannot generate activation key!');
            }

            $activationKey = $this->get255ByteLongValidationKey();
            $isUnique      = $this->isActivationKeyUnique($activationKey);
        } while (!$isUnique);

        return $activationKey;
    }

    /**
     * Checks whether the activation key is unique.
     *
     * @param string $activationKey
     *
     * @return bool
     */
    private function isActivationKeyUnique(string $activationKey): bool
    {
        $options = [
            'entity' => 'Account:User',
            'field'  => 'pendingActivation.key',
        ];

        $violations = $this->validator->validate(
            $activationKey,
            new UniqueProperty($options)
        );

        return count($violations) === 0;
    }

    /**
     * Checks whether a violation exists by the given property and code.
     *
     * @param ConstraintViolationListInterface $violations
     * @param string                           $property
     * @param string                           $code
     *
     * @return bool
     */
    private function hasViolation(
        ConstraintViolationListInterface $violations,
        string $property,
        string $code = UniqueProperty::NON_UNIQUE_PROPERTY
    ): bool {
        /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            if ($violation->getCode() === $code && $violation->getPropertyPath() === $property) {
                return true;
            }
        }

        return false;
    }

    /**
     * Attempts to load a user by its activation key and username.
     *
     * @param string $activationKey
     * @param string $username
     *
     * @return User
     */
    private function findUserByActivationKeyAndUsername($activationKey, string $username): User
    {
        if (!$user = $this->userRepository->findUserByUsernameAndActivationKey($username, $activationKey)) {
            throw $this->createActivationException();
        }

        if ($this->isActivationExpired($user)) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            throw $this->createActivationException();
        }

        return $user;
    }

    /**
     * Checks if the current approval attempt is expired.
     *
     * @param User $user
     *
     * @return bool
     */
    private function isActivationExpired(User $user): bool
    {
        return $user->getPendingActivation()->isActivationExpired();
    }

    /**
     * Creates user suggestions for usernames.
     *
     * @param ConstraintViolationListInterface $violations
     * @param string                           $username
     *
     * @return string[]
     */
    private function generateUsernameSuggestionsByNonUniqueUsername(
        ConstraintViolationListInterface $violations,
        string $username
    ): array {
        return $this->hasViolation($violations, 'username', UniqueProperty::NON_UNIQUE_PROPERTY)
            ? $this->suggestor->getPossibleSuggestions($username)
            : [];
    }

    /**
     * Creates an instance of the activation exception.
     *
     * @return UserActivationException
     */
    private function createActivationException(): UserActivationException
    {
        return new UserActivationException();
    }

    /**
     * Configures internal state data for the non-activated user.
     *
     * @param User $user
     */
    private function buildNotActivatedUser(User $user)
    {
        $user->storeUniqueActivationKeyForNonApprovedUser($this->getUniqueActivationKey());
    }

    /**
     * Generates a 255 byte long activation key.
     *
     * @return string
     */
    private function get255ByteLongValidationKey(): string
    {
        return $this->activationKeyCodeGenerator->generate(255);
    }

    /**
     * Method which checks whether the generator experienced too many generation attempts.
     *
     * @param int $currentAmount
     *
     * @return bool
     */
    private function tooManyGenerationAttempts(int $currentAmount): bool
    {
        return $currentAmount >= 200;
    }
}
