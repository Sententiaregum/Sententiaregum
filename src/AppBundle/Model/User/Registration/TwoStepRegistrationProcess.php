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
use AppBundle\Model\User\Registration\DTO\CreateUserDTO;
use AppBundle\Model\User\Registration\Generator\ActivationKeyCodeGeneratorInterface;
use AppBundle\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PasswordHasherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Implementation of a two step registration process:
 *  Step 1: a simple user will be created with the state "STATE_NEW". The user receives a notification with an activation key.
 *  Step 2: after entering the activation key, the user state will be switched to "STATE_APPROVED". Now the user is able to login.
 *
 * @DI\Service("app.user.registration")
 */
class TwoStepRegistrationProcess implements AccountCreationInterface, AccountApprovalInterface
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
     * Constructor.
     *
     * @param EntityManagerInterface              $entityManager
     * @param ActivationKeyCodeGeneratorInterface $activationKeyCodeGenerator
     * @param ValidatorInterface                  $validator
     * @param EventDispatcherInterface            $eventDispatcher
     * @param PasswordHasherInterface             $passwordHasher
     *
     * @DI\InjectParams({
     *     "entityManager"              = @DI\Inject("doctrine.orm.default_entity_manager"),
     *     "activationKeyCodeGenerator" = @DI\Inject("app.user.registration.activation_key_generator"),
     *     "validator"                  = @DI\Inject("validator"),
     *     "passwordHasher"             = @DI\Inject("ma27_api_key_authentication.password.strategy")
     * })
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ActivationKeyCodeGeneratorInterface $activationKeyCodeGenerator,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher,
        PasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager              = $entityManager;
        $this->activationKeyCodeGenerator = $activationKeyCodeGenerator;
        $this->validator                  = $validator;
        $this->eventDispatcher            = $eventDispatcher;
        $this->hasher                     = $passwordHasher;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UserActivationException If the activation fails
     */
    public function approveByActivationKey($activationKey, $username)
    {
        $repository = $this->entityManager->getRepository('User:User');
        if (!$user = $repository->findOneBy(['activationKey' => $activationKey, 'username' => $username])) {
            throw new UserActivationException();
        }

        $user->setState(User::STATE_APPROVED);
        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }

    /**
     * {@inheritdoc}
     */
    public function registration(CreateUserDTO $userParameters)
    {
        $violations = $this->validator->validate($userParameters);
        if (count($violations) > 0) {
            return $violations;
        }

        $newUser = User::create(
            $userParameters->getUsername(),
            $this->hasher->generateHash($userParameters->getPassword()),
            $userParameters->getEmail()
        );

        $newUser->setLocale($userParameters->getLocale());
        $newUser->addRole($this->entityManager->getRepository('User:Role')->findOneBy(['role' => self::DEFAULT_USER_ROLE]));
        $newUser->setActivationKey($this->activationKeyCodeGenerator->generate(255));

        $this->entityManager->persist($newUser);
        $this->entityManager->flush($newUser);

        /** @var User $persistentUser */
        $persistentUser = $this
            ->entityManager
            ->getRepository('User:User')
            ->findOneBy(['username' => $userParameters->getUsername()]);

        $mailerEvent = (new MailerEvent())
            ->setTemplateSource('AppBundle:Email:activation')
            ->addUser($persistentUser)
            ->addParameter('activation_key', $persistentUser->getActivationKey())
            ->addParameter('username', $persistentUser->getUsername());

        $this->eventDispatcher->dispatch(MailerEvent::EVENT_NAME, $mailerEvent);

        return $persistentUser;
    }
}
