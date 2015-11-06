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

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$header = <<<EOF
This file is part of the Sententiaregum project.

(c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
(c) Ben Bieler <benjaminbieler2014@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

return Config::create()
    ->fixers([
        'header_comment',
        'short_array_syntax',
        'ordered_use',
        'align_double_arrow',
        'align_equals'
    ])
    ->setUsingCache(true)
    ->setUsingLinter(true)
    ->finder(
        DefaultFinder::create()
            ->in(__DIR__)
            ->exclude('src/AppBundle/DataFixtures')
            ->exclude('src/AppBundle/Tests')
            ->exclude('app')
            ->exclude('web')
    );
