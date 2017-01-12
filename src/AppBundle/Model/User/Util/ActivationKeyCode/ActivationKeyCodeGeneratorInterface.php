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

namespace AppBundle\Model\User\Util\ActivationKeyCode;

/**
 * Interface of a keycode generator.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
interface ActivationKeyCodeGeneratorInterface
{
    /**
     * Generates a keycode.
     *
     * @param int $length
     *
     * @return string
     */
    public function generate(int $length = 10): string;
}
