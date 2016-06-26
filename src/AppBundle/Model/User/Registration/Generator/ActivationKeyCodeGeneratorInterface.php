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

namespace AppBundle\Model\User\Registration\Generator;

/**
 * Interface of a keycode generator.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
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
