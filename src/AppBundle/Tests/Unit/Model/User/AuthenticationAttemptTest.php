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

declare(strict_types=1);

namespace AppBundle\Tests\Unit\Model\User;

use AppBundle\Model\User\AuthenticationAttempt;

class AuthenticationAttemptTest extends \PHPUnit_Framework_TestCase
{
    public function testIncreaseCount(): void
    {
        $attempt = new AuthenticationAttempt();
        $this->assertSame(0, $attempt->getAttemptCount());

        $attempt
            ->increaseAttemptCount()
            ->increaseAttemptCount();

        $this->assertSame(2, $attempt->getAttemptCount());
        $this->assertCount(2, $attempt->getLastFailedAttemptTimesInRange());

        $this->assertSame($attempt->getLatestFailedAttemptTime(), $attempt->getLastFailedAttemptTimesInRange()[0]);
    }

    /**
     * @depends testIncreaseCount
     */
    public function testPopLatest(): void
    {
        $model = new AuthenticationAttempt();
        for ($fixtureData = [], $i = 0; $i < 5; $i++) {
            $model->increaseAttemptCount();
            $fixtureData[] = $model->getLatestFailedAttemptTime();
        }

        $range = $model->getLastFailedAttemptTimesInRange();
        $this->assertNotSame($fixtureData[0], end($range));
        $this->assertSame($fixtureData[1], end($range));
    }

    public function testSerialize(): void
    {
        $model = new AuthenticationAttempt();
        $model->setIp('127.0.0.1');
        $model->increaseAttemptCount();

        list($id, $latest, $range) = [
            $model->getId(),
            $model->getLatestFailedAttemptTime(),
            $model->getLastFailedAttemptTimesInRange(),
        ];

        $serialized = serialize($model);
        $new        = unserialize($serialized);

        $this->assertSame($id, $new->getId());
        $this->assertSame('127.0.0.1', $new->getIp());
        $this->assertEquals($latest, $new->getLatestFailedAttemptTime());
    }
}
