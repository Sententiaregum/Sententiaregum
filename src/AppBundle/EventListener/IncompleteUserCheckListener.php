<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\EventListener;

use AppBundle\Model\User\Provider\BlockedAccountReadInterface;
use AppBundle\Model\User\User;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException;

/**
 * Hook which observes the authentication and stops the authentication process if the user is
 * not approved or locked or blocked due to suspicious activity.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class IncompleteUserCheckListener
{
    /**
     * @var BlockedAccountReadInterface
     */
    private $temporaryBlockedAccountProvider;

    /**
     * Constructor.
     *
     * @param BlockedAccountReadInterface $blockedAccountProvider
     */
    public function __construct(BlockedAccountReadInterface $blockedAccountProvider)
    {
        $this->temporaryBlockedAccountProvider = $blockedAccountProvider;
    }

    /**
     * Validates the user during the authentication process.
     *
     * @param OnAuthenticationEvent $event
     *
     * @throws CredentialException If the user is locked
     */
    public function validateUserOnAuthentication(OnAuthenticationEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        $isLocked      = $user->isLocked();
        $isNonApproved = !$user->isApproved();

        if ($isLocked || $isNonApproved || $this->temporaryBlockedAccountProvider->isAccountTemporaryBlocked($user->getId())) {
            switch (true) {
                // NOTE: it's necessary to check `locked` at first now since `locked` is a state transition that can't be done
                // if a user is non-approved, but can be done if the user's approved. Therefore
                // it's safe to rely on $isLocked without looking at `$isNonApproved`, but $isNonApproved is false
                // if the user's locked since this is another state.
                case $isLocked:
                    $message = 'BACKEND_AUTH_LOCKED';
                    break;
                case $isNonApproved:
                    $message = 'BACKEND_AUTH_NON_APPROVED';
                    break;
                default:
                    $message = 'BACKEND_AUTH_BLOCKED';
            }

            throw new CredentialException($message);
        }
    }
}
