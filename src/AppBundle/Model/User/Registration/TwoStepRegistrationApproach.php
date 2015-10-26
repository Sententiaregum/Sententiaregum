<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
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
use AppBundle\Validator\Constraints\UniqueProperty;
use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;
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
 *
 * @DI\Service("app.user.registration")
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
     * Constructor.
     *
     * @param EntityManagerInterface              $entityManager
     * @param ActivationKeyCodeGeneratorInterface $activationKeyCodeGenerator
     * @param ValidatorInterface                  $validator
     * @param EventDispatcherInterface            $eventDispatcher
     * @param PasswordHasherInterface             $passwordHasher
     * @param SuggestorInterface                  $nameSuggestor
     * @param ExpiredActivationProviderInterface  $expiredActivationProvider
     *
     * @DI\InjectParams({
     *     "entityManager"              = @DI\Inject("doctrine.orm.default_entity_manager"),
     *     "activationKeyCodeGenerator" = @DI\Inject("app.user.registration.activation_key_generator"),
     *     "passwordHasher"             = @DI\Inject("ma27_api_key_authentication.password.strategy"),
     *     "nameSuggestor"              = @DI\Inject("app.user.registration.name_suggestor"),
     *     "expiredActivationProvider"  = @DI\Inject("app.redis.cluster.approval")
     * })
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ActivationKeyCodeGeneratorInterface $activationKeyCodeGenerator,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher,
        PasswordHasherInterface $passwordHasher,
        SuggestorInterface $nameSuggestor,
        ExpiredActivationProviderInterface $expiredActivationProvider
    ) {
        $this->entityManager              = $entityManager;
        $this->activationKeyCodeGenerator = $activationKeyCodeGenerator;
        $this->validator                  = $validator;
        $this->eventDispatcher            = $eventDispatcher;
        $this->hasher                     = $passwordHasher;
        $this->suggestor                  = $nameSuggestor;
        $this->expiredActivationProvider  = $expiredActivationProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UserActivationException If the activation fails
     */
    public function approveByActivationKey($activationKey, $username)
    {
        $user = $this->findUserByActivationKeyAndUsername($activationKey, $username);

        $user->setState(User::STATE_APPROVED);
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

        $newUser = $this->getRegisteredUser($userParameters);
        $this->sendActivationEmail($newUser);
        $this->expiredActivationProvider->attachNewApproval($newUser->getActivationKey());

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
        $newUser->addRole($this->getDefaultRole());

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
     * Gets the default role of a new user.
     *
     * @return \AppBundle\Model\User\Role
     */
    private function getDefaultRole()
    {
        return $this->entityManager->getRepository('Account:Role')->findOneBy(['role' => self::DEFAULT_USER_ROLE]);
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
        $repository = $this->entityManager->getRepository('Account:User');
        $query      = ['activationKey' => $activationKey, 'username' => $username];

        if ((!$user = $repository->findOneBy($query)) || $this->isActivationExpired($activationKey)) {
            throw new UserActivationException();
        }

        return $user;
    }

    /**
     * Checks if the current approval attempt is expired.
     *
     * @param string $key
     *
     * @return bool
     */
    private function isActivationExpired($key)
    {
        return !$this->expiredActivationProvider->checkApprovalByActivationKey($key);
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
     * Fetches the registered user by its parameters.
     *
     * @param CreateUserDTO $userParameters
     *
     * @return User
     */
    private function getRegisteredUser(CreateUserDTO $userParameters)
    {
        return $this
            ->entityManager
            ->getRepository('Account:User')
            ->findOneBy(['username' => $userParameters->getUsername()]);
    }
}
