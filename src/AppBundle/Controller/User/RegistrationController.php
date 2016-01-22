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

namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Exception\UserActivationException;
use AppBundle\Model\User\Registration\DTO\CreateUserDTO;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * API for the registration implementation.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class RegistrationController extends BaseController
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
     * @ParamConverter(name="dto", class="AppBundle\Model\User\Registration\DTO\CreateUserDTO")
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

            return View::create($response, Codes::HTTP_BAD_REQUEST);
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
            return View::create(null, Codes::HTTP_FORBIDDEN);
        }
    }
}
