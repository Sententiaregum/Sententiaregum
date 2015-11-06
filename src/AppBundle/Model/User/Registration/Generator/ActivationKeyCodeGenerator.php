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

namespace AppBundle\Model\User\Registration\Generator;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Generator the builds the activation key.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 *
 * @DI\Service(id="app.user.registration.activation_key_generator")
 */
class ActivationKeyCodeGenerator implements ActivationKeyCodeGeneratorInterface
{
    /**
     * @var string
     */
    private $randomKeys = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890$!~';

    /**
     * {@inheritdoc}
     */
    public function generate($length = 10)
    {
        $string = '';
        $maxKey = strlen($this->randomKeys) - 1;

        for ($i = 0; $i < $length; ++$i) {
            $string .= $this->randomKeys[mt_rand(0, $maxKey)];
        }

        return $string;
    }
}
