<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\EventListener;

use AppBundle\Model\User\User;
use JMS\DiExtraBundle\Annotation as DI;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException;

/**
 * Hook which observes the authentication and stops the authentication process if the user is not approved or locked.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service
 */
class IncompleteUserCheckListener
{
    /**
     * Validates the user during the authentication process.
     *
     * @param OnAuthenticationEvent $event
     *
     * @throws CredentialException If the user is locked
     *
     * @DI\Observe(event="ma27_api_key_authentication.authentication", priority=255)
     */
    public function validateUserOnAuthentication(OnAuthenticationEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        $isLocked      = $user->isLocked();
        $isNonApproved = $user->getState() !== User::STATE_APPROVED;

        if ($isLocked || $isNonApproved) {
            $message = $isNonApproved ? 'BACKEND_AUTH_NON_APPROVED' : 'BACKEND_AUTH_LOCKED';

            throw new CredentialException($message);
        }
    }
}
