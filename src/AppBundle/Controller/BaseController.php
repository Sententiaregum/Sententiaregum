<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Abstract controller that contains some basic utilities.
 */
abstract class BaseController extends Controller
{
    /**
     * Converts all validation errors to a flat array that can be serialized by the jms serializer.
     *
     * @param ConstraintViolationListInterface $constraintViolations
     *
     * @return string[]
     */
    protected function transformValidationErrorsToArray(ConstraintViolationListInterface $constraintViolations)
    {
        $result = [];

        /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
        foreach ($constraintViolations as $violation) {
            if (!isset($result[$violation->getPropertyPath()])) {
                $result[$violation->getPropertyPath()] = [];
            }

            $result[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $result;
    }
}
