<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
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
     *     statusCodes={201="Successful creation", 400="Invalid parameters"}
     * )
     *
     * Route that contains the first step of the registration
     *
     * @param CreateUserDTO $dto
     *
     * @return mixed[]
     *
     * @Rest\Post("/users.{_format}", name="app.user.create")
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
     *     statusCodes={204="Successful activation", 403="Invalid activation key"}
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @Rest\Patch("/users/activate.{_format}", name="app.user.activate")
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
