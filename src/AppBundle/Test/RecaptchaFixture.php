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

namespace AppBundle\Test;

use ReCaptcha\ReCaptcha;

/**
 * RecaptchaFixture.
 *
 * @author Benjamin Bieler <benjaminbieler2014@gmail.com>
 */
class RecaptchaFixture extends ReCaptcha
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // everything is kept simple here, just for testing purposes
        parent::__construct('my-test-secret');
    }

    /**
     * {@inheritdoc}
     */
    public function verify($response, $remoteIp = null)
    {
        // just a mockup to avoid API calls against the google API during integration tests.
        return new class() {
            /**
             * @return bool
             */
            public function isSuccess()
            {
                return true;
            }
        };
    }
}
