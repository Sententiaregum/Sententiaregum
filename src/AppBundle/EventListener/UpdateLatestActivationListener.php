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

use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Hook that updates the latest activation of a user.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class UpdateLatestActivationListener
{
    /**
     * Hook that updates the last action after the authentication.
     *
     * @param OnAuthenticationEvent $event
     */
    public function updateOnLogin(OnAuthenticationEvent $event)
    {
        /** @var \AppBundle\Model\User\User $user */
        $user = $event->getUser();

        // saving is not necessary since the user will be updated after triggering
        // this event during the login
        $user->updateLastAction();
    }
}
