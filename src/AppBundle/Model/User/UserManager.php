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

use AppBundle\Model\User\Data\DTO\CreateUserDTO;
use AppBundle\Model\User\Generator\ActivationKeyCodeGeneratorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\DiExtraBundle\Annotation as DI;
use Sonata\CoreBundle\Model\BaseEntityManager;
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
     * Constructor.
     *
     * @param string                              $class
     * @param ManagerRegistry                     $registry
     * @param ActivationKeyCodeGeneratorInterface $generator
     * @param ValidatorInterface                  $validator
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
        ValidatorInterface $validator
    ) {
        parent::__construct($class, $registry);

        $this->activationKeyGenerator = $generator;
        $this->validator              = $validator;
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

        /** @var User $newUser */
        $newUser = $this->create();
        $newUser->setUsername($userParameters->getUsername());
        $newUser->setPassword($userParameters->getPassword());
        $newUser->setEmail($userParameters->getEmail());
        $newUser->setLocale($userParameters->getLocale());
        $newUser->setActivationKey($this->activationKeyGenerator->generate(255));

        $this->save($newUser);

        return $this->getRepository()->findOneBy(['username' => $userParameters->getUsername()]);
    }
}
