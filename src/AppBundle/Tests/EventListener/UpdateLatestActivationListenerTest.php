<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\EventListener;

use AppBundle\EventListener\UpdateLatestActivationListener;
use AppBundle\Model\User\User;
use AppBundle\Model\User\UserManagerInterface;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UpdateLatestActivationListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateLastActionAfterLogin()
    {
        $listener = new UpdateLatestActivationListener(
            $this->getMock(UserManagerInterface::class),
            $this->getMock(TokenStorageInterface::class),
            $this->getRequestStack()
        );

        $user     = User::create('Ma27', 'foo', 'foo@bar.de');
        $dateTime = new \DateTime('-5 minutes');

        $user->setLastAction($dateTime);
        $listener->updateOnLogin(new OnAuthenticationEvent($user));

        $this->assertGreaterThan($dateTime->getTimestamp(), $user->getLastAction()->getTimestamp());
    }

    public function testUpdateUserOnKernelTermination()
    {
        $userManager = $this->getMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('save');

        $user     = User::create('Ma27', 'foo', 'foo@bar.de');
        $dateTime = new \DateTime('-5 minutes');

        $user->setLastAction($dateTime);

        $storage = $this->getMock(TokenStorageInterface::class);
        $storage
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(new UsernamePasswordToken($user, 'Ma27@foo', 'unit-test')));

        $listener = new UpdateLatestActivationListener($userManager, $storage, $this->getRequestStack());
        $listener->updateAfterRequest();

        $this->assertGreaterThan($dateTime->getTimestamp(), $user->getLastAction()->getTimestamp());
    }

    public function testUpdateOnNonProtectedRoute()
    {
        $userManager = $this->getMock(UserManagerInterface::class);
        $userManager->expects($this->never())->method('save');

        $storage = $this->getMock(TokenStorageInterface::class);
        $storage
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null));

        $listener = new UpdateLatestActivationListener($userManager, $storage, $this->getRequestStack());
        $listener->updateAfterRequest();
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
