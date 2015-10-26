<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
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
