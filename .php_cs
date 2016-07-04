<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use Symfony\Component\Finder\Finder;
use Symfony\CS\Config\Config;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;
use Symfony\CS\FixerInterface;

$header = <<<EOF
This file is part of the Sententiaregum project.

(c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
(c) Ben Bieler <benjaminbieler2014@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

$finder = Finder::create()
    ->files()
    ->in(['app', 'src/AppBundle'])
    ->name('*.php');

return Config::create()
    ->finder($finder)
    ->level(FixerInterface::CONTRIB_LEVEL)
    ->setUsingCache(true)
;
