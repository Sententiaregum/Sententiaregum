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
