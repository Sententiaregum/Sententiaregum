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

namespace AppBundle\Model\User\Registration;

use AppBundle\Event\MailerEvent;
use AppBundle\Exception\UserActivationException;
use AppBundle\Model\User\Registration\Activation\ExpiredActivationProviderInterface;
use AppBundle\Model\User\Registration\DTO\CreateUserDTO;
use AppBundle\Model\User\Registration\Generator\ActivationKeyCodeGeneratorInterface;
use AppBundle\Model\User\Registration\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Model\User\Registration\Value\Result;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserRepository;
use AppBundle\Validator\Constraints\UniqueProperty;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
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
 * NOTE: the email validator checks the host, too, so accounts with not-existing hosts are impossible now.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class TwoStepRegistrationApproach implements AccountCreationInterface, AccountApprovalInterface
{
    const DEFAULT_USER_ROLE = 'ROLE_USER';

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
     * @var ExpiredActivationProviderInterface
     */
    private $expiredActivationProvider;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityRepository
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
     * @param ExpiredActivationProviderInterface  $expiredActivationProvider
     * @param UserRepository                      $userRepository
     * @param EntityRepository                    $roleRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ActivationKeyCodeGeneratorInterface $activationKeyCodeGenerator,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher,
        PasswordHasherInterface $passwordHasher,
        SuggestorInterface $nameSuggestor,
        ExpiredActivationProviderInterface $expiredActivationProvider,
        UserRepository $userRepository,
        EntityRepository $roleRepository
    ) {
        $this->entityManager              = $entityManager;
        $this->activationKeyCodeGenerator = $activationKeyCodeGenerator;
        $this->validator                  = $validator;
        $this->eventDispatcher            = $eventDispatcher;
        $this->hasher                     = $passwordHasher;
        $this->suggestor                  = $nameSuggestor;
        $this->expiredActivationProvider  = $expiredActivationProvider;
        $this->userRepository             = $userRepository;
        $this->roleRepository             = $roleRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UserActivationException If the activation fails
     * @throws \RuntimeException       If the role is not present
     */
    public function approveByActivationKey($activationKey, $username)
    {
        $user = $this->findUserByActivationKeyAndUsername($activationKey, $username);
        $user->setState(User::STATE_APPROVED);

        // if the purger runs a bulk delete on users
        // there occur foreign key constraint issues with the
        // roles. Therefore roles are only allowed for
        // approved users.
        $defaultRole = $this->roleRepository->findOneBy(['role' => self::DEFAULT_USER_ROLE]);
        if (!$defaultRole) {
            throw new \RuntimeException(sprintf(
                'Role "%s" is not present!',
                self::DEFAULT_USER_ROLE
            ));
        }

        $user->addRole($defaultRole);

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }

    /**
     * {@inheritdoc}
     */
    public function registration(CreateUserDTO $userParameters)
    {
        $result = $this->buildAndValidateUserModelByDTO($userParameters);
        if (!$result['valid']) {
            return new Result(
                $result['violations'],
                $this->generateUsernameSuggestionsByNonUniqueUsername(
                    $result['violations'],
                    $userParameters->getUsername()
                )
            );
        }

        $user = $result['user'];

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        $this->sendActivationEmail($user);
        $this->expiredActivationProvider->attachNewApproval($user->getActivationKey());

        return new Result(null, [], $user);
    }

    /**
     * Builds a new user and validates it.
     *
     * @param CreateUserDTO $userParameters
     *
     * @return mixed[]
     */
    private function buildAndValidateUserModelByDTO(CreateUserDTO $userParameters)
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
            $this->hasher->generateHash($userParameters->getPassword()),
            $userParameters->getEmail()
        );

        $newUser->setLocale($userParameters->getLocale());
        $newUser->setActivationKey($this->getUniqueActivationKey());

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
            ->setTemplateSource('AppBundle:Email:activation')
            ->addUser($persistentUser)
            ->addParameter('activation_key', $persistentUser->getActivationKey())
            ->addParameter('username', $persistentUser->getUsername());

        $this->eventDispatcher->dispatch(MailerEvent::EVENT_NAME, $mailerEvent);
    }

    /**
     * Generates an unique activation key.
     *
     * @return int
     */
    private function getUniqueActivationKey()
    {
        $rounds = 0;

        do {
            ++$rounds;

            if ($rounds === 200) {
                throw new \OverflowException('Cannot generate activation key!');
            }

            $activationKey = $this->activationKeyCodeGenerator->generate(255);
            $isUnique      = $this->isActivationKeyUnique($activationKey);
        } while (!$isUnique);

        return $activationKey;
    }

    /**
     * Checks whether the activation key is unique.
     *
     * @param int $activationKey
     *
     * @return bool
     */
    private function isActivationKeyUnique($activationKey)
    {
        $options = [
            'entity' => 'Account:User',
            'field'  => 'activationKey',
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
        $property,
        $code = UniqueProperty::NON_UNIQUE_PROPERTY
    ) {
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
    private function findUserByActivationKeyAndUsername($activationKey, $username)
    {
        if (!$user = $this->userRepository->findUserByUsernameAndActivationKey($username, $activationKey)) {
            throw $this->createActivationException();
        } elseif ($this->isActivationExpired($user)) {
            $this->entityManager->remove($user);
            $this->entityManager->flush($user);

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
    private function isActivationExpired(User $user)
    {
        return !$this->expiredActivationProvider->checkApprovalByUser($user);
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
        $username
    ) {
        return $this->hasViolation($violations, 'username', UniqueProperty::NON_UNIQUE_PROPERTY)
            ? $this->suggestor->getPossibleSuggestions($username)
            : [];
    }

    /**
     * Creates an instance of the activation exception.
     *
     * @return UserActivationException
     */
    private function createActivationException()
    {
        return new UserActivationException();
    }
}
