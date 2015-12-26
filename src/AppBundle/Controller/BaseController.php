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

use AppBundle\Model\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Abstract controller that contains some basic utilities.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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
    protected function sortViolationMessagesByPropertyPath(ConstraintViolationListInterface $constraintViolations)
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

    /**
     * Gets the current user authenticated against firewall.
     *
     * @return User
     */
    protected function getCurrentUser()
    {
        $user = $this->getUser();
        if ($user && !$user instanceof User) {
            throw new \RuntimeException(sprintf(
                'Expect user object of instance "%s", but found "%s"!',
                User::class,
                get_class($user)
            ));
        }

        return $user;
    }
}
