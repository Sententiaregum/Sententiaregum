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

namespace AppBundle\Controller;

use AppBundle\Exception\UserActivationException;
use AppBundle\Model\User\DTO\CreateUserDTO;
use AppBundle\Model\User\Value\Credentials;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * API for the user resource.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UserController extends BaseController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Creates a new user",
     *     statusCodes={201="Successful creation", 400="Invalid parameters"},
     *     requirements={
     *         {"name"="_format", "dataType"="string", "requirement"="^(json|xml)$", "description"="Data format to return"}
     *     },
     *     parameters={
     *         {"name"="username", "dataType"="string", "required"=true, "description"="Name of the new user"},
     *         {"name"="password", "dataType"="string", "required"=true, "description"="Password of the new user"},
     *         {"name"="email", "dataType"="string", "required"=true, "description"="Email of the new user"},
     *         {"name"="locale", "dataType"="string", "required"=true, "description"="Locale/Language of the new user"},
     *     }
     * )
     *
     * Route that contains the first step of the registration
     *
     * @param CreateUserDTO $dto
     *
     * @return mixed[]
     *
     * @Rest\Post("/users.{_format}", name="app.user.create", requirements={"_format"="^(json|xml)$"})
     * @Rest\View(statusCode=201)
     *
     * @ParamConverter(name="dto", class="AppBundle\Model\User\DTO\CreateUserDTO")
     */
    public function createUserAction(CreateUserDTO $dto)
    {
        /** @var \AppBundle\Model\User\Registration\TwoStepRegistrationApproach $registrator */
        $registrator = $this->get('app.user.registration');

        $result = $registrator->registration($dto);
        if (!$result->isValid()) {
            $response = ['errors' => $this->sortViolationMessagesByPropertyPath($result->getViolations())];
            if (!empty($result->getSuggestions())) {
                $response['name_suggestions'] = $result->getSuggestions();
            }

            return View::create($response, Response::HTTP_BAD_REQUEST);
        }

        return ['id' => $result->getUser()->getId()];
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Activates the recently created user",
     *     statusCodes={204="Successful activation", 403="Invalid activation key"},
     *     requirements={
     *         {"name"="_format", "dataType"="string", "requirement"="^(json|xml)$", "description"="Data format to return"}
     *     }
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @Rest\Patch("/users/activate.{_format}", name="app.user.activate", requirements={"_format"="^(json|xml)$"})
     * @Rest\View(statusCode=204)
     *
     * @Rest\QueryParam(name="username", description="Name of the user to activate")
     * @Rest\QueryParam(name="activation_key", description="Activation key")
     */
    public function activateUserAction(ParamFetcher $paramFetcher)
    {
        /** @var \AppBundle\Model\User\Registration\TwoStepRegistrationApproach $registrator */
        $registrator = $this->get('app.user.registration');

        try {
            $registrator->approveByActivationKey($paramFetcher->get('activation_key'), $paramFetcher->get('username'));
        } catch (UserActivationException $ex) {
            return View::create(null, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Provides information about the currently logged-in user",
     *     statusCodes={401="Unauthorized", 200="Successful request for credential details"},
     *     requirements={
     *         {"name"="_format", "dataType"="string", "requirement"="^(json|xml)$", "description"="Data format to return"}
     *     },
     * )
     *
     * Renders details of the user credentials.
     *
     * @return Credentials
     *
     * @Rest\Get("/protected/users/credentials.{_format}", name="app.user.credentials", requirements={"_format"="^(json|xml)$"})
     * @Rest\View(statusCode=200)
     */
    public function getCredentialInformationAction()
    {
        return Credentials::fromEntity($this->getCurrentUser());
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Creates a list of followers that contains a list showing which followers are online",
     *     statusCodes={200="Successful generation","401"="Unauthorized"},
     *     requirements={
     *         {"name"="_format", "dataType"="string", "requirement"="^(json|xml)$", "description"="Data format to return"}
     *     }
     * )
     *
     * Controller action that creates a list of users the current user follows that shows which users are online.
     *
     * @return bool[]
     *
     * @Rest\Get("/protected/users/online.{_format}", name="app.user.online", requirements={"_format"="^(json|xml)$"})
     * @Rest\View
     */
    public function onlineFollowingListAction()
    {
        /** @var \AppBundle\Model\User\Online\OnlineUserIdDataProviderInterface $cluster */
        $cluster        = $this->get('app.redis.cluster.online_users');
        $userRepository = $this->getDoctrine()->getRepository('Account:User');
        $currentUser    = $this->getCurrentUser();

        return $cluster->validateUserIds($userRepository->getFollowingIdsByUser($currentUser));
    }
}
