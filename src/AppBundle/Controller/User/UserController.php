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
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends BaseController
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
     * @View(statusCode=201)
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
        /** @var \AppBundle\Model\User\Registration\TwoStepRegistrationProcess $userManager */
        $userManager = $this->get('app.user.registration');

        $result = $userManager->registration($dtoBuilder->buildRegistrationDTO($paramFetcher));
        if ($result instanceof ConstraintViolationListInterface) {
            return $this->transformValidationErrorsToArray($result);
        }

        return ['id' => $result->getId()];
    }
}
