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

namespace AppBundle\Tests\Unit\EventListener;

use AppBundle\EventListener\UpdateLatestActivationListener;
use AppBundle\Model\User\User;
use Ma27\ApiKeyAuthenticationBundle\Event\OnAuthenticationEvent;
use Ma27\ApiKeyAuthenticationBundle\Model\Password\PhpPasswordHasher;

class UpdateLatestActivationListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateLastActionAfterLogin(): void
    {
        $listener = new UpdateLatestActivationListener();

        $user     = User::create('Ma27', 'foo', 'foo@bar.de', new PhpPasswordHasher());
        $dateTime = new \DateTime('-5 minutes');

        $user->updateLastAction();
        $listener->updateOnLogin(new OnAuthenticationEvent($user));

        $this->assertGreaterThan($dateTime->getTimestamp(), $user->getLastAction()->getTimestamp());
    }
}
