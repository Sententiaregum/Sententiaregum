<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User;

use AppBundle\Event\MailerEvent;
use AppBundle\Model\User\Data\DTO\CreateUserDTO;
use AppBundle\Model\User\Generator\ActivationKeyCodeGeneratorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\DiExtraBundle\Annotation as DI;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Manager which is responsible for the business logic of the user.
 *
 * @DI\Service(id="app.user.user_manager")
 */
class UserManager extends BaseEntityManager implements UserManagerInterface
{
    /**
     * @var ActivationKeyCodeGeneratorInterface
     */
    private $activationKeyGenerator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructor.
     *
     * @param string                              $class
     * @param ManagerRegistry                     $registry
     * @param ActivationKeyCodeGeneratorInterface $generator
     * @param ValidatorInterface                  $validator
     * @param EventDispatcherInterface            $eventDispatcher
     *
     * @DI\InjectParams({
     *     "class" = @DI\Inject("%app.model.user%"),
     *     "registry" = @DI\Inject("doctrine"),
     *     "generator" = @DI\Inject("app.user.activation_key_generator")
     * })
     */
    public function __construct(
        $class,
        ManagerRegistry $registry,
        ActivationKeyCodeGeneratorInterface $generator,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($class, $registry);

        $this->activationKeyGenerator = $generator;
        $this->validator              = $validator;
        $this->eventDispatcher        = $eventDispatcher;
    }

    /**
     * Processes the first step of the registration.
     *
     * @param CreateUserDTO $userParameters
     *
     * @return User|\Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function registration(CreateUserDTO $userParameters)
    {
        $violations = $this->validator->validate($userParameters);
        if (count($violations) > 0) {
            return $violations;
        }

        $this->save($this->buildUserEntity($userParameters));

        /** @var User $persistentUser */
        $persistentUser = $this->getRepository()->findOneBy(['username' => $userParameters->getUsername()]);
        $this->eventDispatcher->dispatch(MailerEvent::EVENT_NAME, $this->prepareMailObject($persistentUser));

        return $persistentUser;
    }

    /**
     * Creates a fresh user model.
     *
     * @param CreateUserDTO $DTO
     *
     * @return User
     */
    private function buildUserEntity(CreateUserDTO $DTO)
    {
        /** @var User $newUser */
        $newUser = $this->create();
        $newUser->setUsername($DTO->getUsername());
        $newUser->setPassword($DTO->getPassword());
        $newUser->setEmail($DTO->getEmail());
        $newUser->setLocale($DTO->getLocale());
        $newUser->setActivationKey($this->activationKeyGenerator->generate(255));
        
        return $newUser;
    }

    /**
     * Prepares the mailer event object.
     *
     * @param User $persistentUser
     *
     * @return MailerEvent
     */
    private function prepareMailObject(User $persistentUser)
    {
        $mailerEvent = new MailerEvent();
        $mailerEvent->setTemplateSource('@AppBundle/Email/activation');
        $mailerEvent->addUser($persistentUser);
        $mailerEvent->addParameter('activation_key', $persistentUser->getActivationKey());
        $mailerEvent->addParameter('username', $persistentUser->getUsername());

        return $mailerEvent;
    }
}
