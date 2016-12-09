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

namespace AppBundle\Tests\View;

use AppBundle\View\I18nResponseFormatBuilder;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class I18nResponseFormatBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param ConstraintViolationList $violationList
     * @param TranslatorInterface     $translator
     * @param bool                    $sortFlag
     * @param bool                    $languageFlag
     * @param array                   $expected
     * @param array                   $target
     *
     * @dataProvider provideMockData
     */
    public function testBehavior(
        ConstraintViolationList $violationList,
        TranslatorInterface $translator,
        bool $sortFlag,
        bool $languageFlag,
        array $expected,
        array $target = []
    ) {
        $service = new I18nResponseFormatBuilder($translator);
        $this->assertSame(
            $expected,
            $service->formatTranslatableViolationList($violationList, $sortFlag, $languageFlag, $target)
        );
    }

    /**
     * @param bool   $allLanguages
     * @param array  $target
     * @param string $expectedMessage
     *
     * @dataProvider provideErrorCases
     */
    public function testExceptionThrownWithInvalidTargetLocales(bool $allLanguages, array $target, string $expectedMessage)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $service = new I18nResponseFormatBuilder(new IdentityTranslator());
        $service->formatTranslatableViolationList(
            new ConstraintViolationList([$this->createMock(ConstraintViolation::class)]),
            true,
            $allLanguages,
            $target
        );
    }

    public function testPluralization()
    {
        $translator = new IdentityTranslator();
        $translator->setLocale('fr');

        $service    = new I18nResponseFormatBuilder($translator);
        $violations = new ConstraintViolationList();

        $violations->add(new ConstraintViolation(
            'Two apples',
            '{0}No apples|{1}One apple|]2,Inf]%count% apples',
            ['%count%' => 1],
            null,
            'property',
            'anything',
            1
        ));

        $result = $service->formatTranslatableViolationList(
            $violations,
            false,
            true,
            ['en']
        );

        $this->assertSame('One apple', $result['en'][0]);
    }

    public function testTransChoiceFails()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())
            ->method('transChoice')
            ->will($this->returnCallback(
                function () {
                    throw new \InvalidArgumentException('The transChoice() failed.');
                }
            ));

        $translator
            ->expects($this->once())
            ->method('trans')
            ->willReturn('Another translation.');

        $violation = new ConstraintViolation('Translation.', 'Tpl.', [], 'root', 'property', 'invalid value', 2);
        $list      = new ConstraintViolationList([$violation]);

        $service = new I18nResponseFormatBuilder($translator);
        $result  = $service->formatTranslatableViolationList($list, true, true, ['en']);

        $this->assertSame($result['property']['en'][0], 'Another translation.');
    }

    public function provideMockData()
    {
        $violation  = new ConstraintViolation('Damn error!', 'Damn error!', [], null, 'property', 'blah');
        $violation2 = new ConstraintViolation('Another error!', 'Another error!', [], null, 'property', 'foobar');
        $translator = new IdentityTranslator();
        $translator->setLocale('de');

        return [
            'Only default language, with sort' => [
                new ConstraintViolationList([$violation]),
                $translator,
                true,
                false,
                [
                    'property' => ['Damn error!'],
                ],
            ],
            'Only default language, no sort'   => [
                new ConstraintViolationList([$violation]),
                $translator,
                false,
                false,
                [
                    'Damn error!',
                ],
            ],
            'All languages, no sort'           => [
                new ConstraintViolationList([$violation]),
                $translator,
                false,
                true,
                [
                    'de' => ['Damn error!'],
                    'en' => ['Damn error!'],
                ],
                ['de', 'en'],
            ],
            'All languages, with sort'         => [
                new ConstraintViolationList([$violation, $violation2]),
                $translator,
                true,
                true,
                [
                    'property' => [
                        'de' => ['Damn error!', 'Another error!'],
                        'en' => ['Damn error!', 'Another error!'],
                    ],
                ],
                ['de', 'en'],
            ],
        ];
    }

    public function provideErrorCases()
    {
        return [
            'All languages, but no target' => [true, [], 'Wrong usage of $targetLocales: If the all locales should be rendered, $targetLocales must be given!'],
            'No languages, but target'     => [false, ['de', 'en'], 'Wrong usage of $targetLocales: If the default locale is the only target, $targetLocales must not have any values!'],
        ];
    }
}
