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

namespace AppBundle\Tests\Unit\Service\PHPCS\Fixer;

use AppBundle\Service\PHPCS\Fixer\StrictTypeDeclarationFixer;
use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class StrictTypeDeclarationFixerTest extends AbstractFixerTestBase
{
    /**
     * @param string $expected
     * @param string $actual
     *
     * @dataProvider provideFixtureData
     */
    public function testDeclarationSpace(string $expected, string $actual)
    {
        $this->makeTest($expected, $actual);
    }

    /**
     * @return array
     */
    public function provideFixtureData(): array
    {
        $expected = implode(PHP_EOL, ['<?php', 'declare(strict_types=1);']);

        return [
            [
                $expected,
                implode(PHP_EOL, ['<?php', 'declare (strict_types=1);']),
            ],
            [
                $expected,
                implode(PHP_EOL, ['<?php', 'declare(strict_types = 1);']),
            ],
            [
                $expected,
                implode(PHP_EOL, ['<?php', 'declare  (strict_types = 1);']),
            ],
            [
                $expected,
                implode(PHP_EOL, ['<?php', 'declare(strict_types  = 1);']),
            ],
            [
                $expected,
                implode(PHP_EOL, ['<?php', 'declare(strict_types    = 1);']),
            ],
            [
                $expected,
                implode(PHP_EOL, ['<?php', 'declare(strict_types        =    1);']),
            ],
            [
                $expected,
                implode(PHP_EOL, ['<?php', "declare(strict_types \r= 1);"]),
            ],
            [
                $expected,
                implode(PHP_EOL, ['<?php', "declare(strict_types\r=\r1);"]),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixer()
    {
        return new StrictTypeDeclarationFixer();
    }
}
