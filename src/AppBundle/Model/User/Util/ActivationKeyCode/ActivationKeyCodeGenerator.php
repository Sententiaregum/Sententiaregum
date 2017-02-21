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
 * Generator the builds the activation key.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class ActivationKeyCodeGenerator implements ActivationKeyCodeGeneratorInterface
{
    /**
     * @var string
     */
    private $randomKeys = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

    /**
     * {@inheritdoc}
     */
    public function generate(int $length = 10): string
    {
        for ($i = 0, $maxKey = strlen($this->randomKeys) - 1, $string = ''; $i < $length; ++$i) {
            $string .= $this->randomKeys[mt_rand(0, $maxKey)];
        }

        return $string;
    }
}
