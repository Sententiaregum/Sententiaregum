<?php

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$header = <<<EOF
This file is part of the sententiaregum application.

Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS

@copyright (c) 2015 Sententiaregum
Please check out the license file in the document root of this application
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
