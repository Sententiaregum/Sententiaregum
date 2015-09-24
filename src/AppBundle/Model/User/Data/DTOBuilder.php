<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User\Data;

use AppBundle\Model\User\Registration\DTO\CreateUserDTO;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Aggregator class that turns http parameters from the ParamFetcher into a data transfer object.
 *
 * @DI\Service(id="app.user.dto_builder")
 */
class DTOBuilder
{
    /**
     * Aggregates request parameters into the data transfer object.
     *
     * @param ParamFetcher $requestParameters
     *
     * @return \AppBundle\Model\User\Registration\DTO\CreateUserDTO
     */
    public function buildRegistrationDTO(ParamFetcher $requestParameters)
    {
        $dto = new CreateUserDTO();

        $dto->setUsername($requestParameters->get('username'));
        $dto->setPassword($requestParameters->get('password'));
        $dto->setEmail($requestParameters->get('email'));
        $dto->setLocale($requestParameters->get('locale'));

        return $dto;
    }
}
