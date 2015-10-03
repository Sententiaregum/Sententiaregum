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
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;

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
     * @param ParamFetcher $paramFetcher
     *
     * @return mixed[]
     *
     * @Post("/users.{_format}", name="app.user.create")
     * @RestView(statusCode=201)
     *
     * @RequestParam(name="username", description="Name of the new user")
     * @RequestParam(name="password", description="Password of the new user")
     * @RequestParam(name="email", description="Email of the new user")
     * @RequestParam(name="locale", description="Locale of the new user")
     */
    public function createUserAction(ParamFetcher $paramFetcher)
    {
        /** @var \AppBundle\Model\User\Data\DTOBuilder $dtoBuilder */
        $dtoBuilder = $this->get('app.user.dto_builder');
        /** @var \AppBundle\Model\User\Registration\TwoStepRegistrationProcess $registrator */
        $registrator = $this->get('app.user.registration');

        $result = $registrator->registration($dtoBuilder->buildRegistrationDTO($paramFetcher));
        if ($result instanceof ConstraintViolationListInterface) {
            return View::create($this->sortViolationMessagesByPropertyPath($result), Codes::HTTP_BAD_REQUEST);
        }

        return ['id' => $result->getId()];
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
     * @Patch("/users/activate.{_format}", name="app.user.activate")
     * @RestView(statusCode=204)
     *
     * @QueryParam(name="username", description="Name of the user to activate")
     * @QueryParam(name="activation_key", description="Activation key")
     */
    public function activateUserAction(ParamFetcher $paramFetcher)
    {
        /** @var \AppBundle\Model\User\Registration\TwoStepRegistrationProcess $registrator */
        $registrator = $this->get('app.user.registration');

        try {
            $registrator->approveByActivationKey($paramFetcher->get('activation_key'), $paramFetcher->get('username'));
        } catch (UserActivationException $ex) {
            return View::create(null, Codes::HTTP_FORBIDDEN);
        }
    }
}
