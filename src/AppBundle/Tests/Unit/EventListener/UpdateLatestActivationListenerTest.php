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

namespace AppBundle\Tests\Unit\EventListener;

use AppBundle\EventListener\UpdateLatestActivationListener;
use AppBundle\Model\User\User;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UpdateLatestActivationListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateLastActionAfterLogin()
    {
        $listener = new UpdateLatestActivationListener(
            $this->getRequestStack()
        );

        $user     = User::create('Ma27', 'foo', 'foo@bar.de', new PhpPasswordHasher());
        $dateTime = new \DateTime('-5 minutes');

        $user->updateLastAction();
        $listener->updateOnLogin(new OnAuthenticationEvent($user));

        $this->assertGreaterThan($dateTime->getTimestamp(), $user->getLastAction()->getTimestamp());
    }

    /**
     * @return RequestStack
     */
    private function getRequestStack()
    {
        $request = Request::create('/');
        $request->server->set('REQUEST_TIME', time());

        $stack = new RequestStack();
        $stack->push($request);

        return $stack;
    }
}
