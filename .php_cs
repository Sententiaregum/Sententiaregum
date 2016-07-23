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

use SLLH\StyleCIBridge\ConfigBridge as Config;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$header = <<<EOF
This file is part of the Sententiaregum project.

(c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
(c) Ben Bieler <benjaminbieler2014@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

/** @var \Symfony\CS\Config\Config $config */
$config = Config::create();

return $config
    ->addCustomFixer(new \AppBundle\Service\PHPCS\Fixer\StrictTypeDeclarationFixer())
    ->setUsingCache(true)
;
