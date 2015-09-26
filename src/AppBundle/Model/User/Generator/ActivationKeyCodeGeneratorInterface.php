<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Model\User\Generator;

/**
 * Interface of a keycode generator.
 */
interface ActivationKeyCodeGeneratorInterface
{
    /**
     * Generates a keycode.
     *
     * @param int $length
     *
     * @return int
     */
    public function generate($length = 10);
}
