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

namespace AppBundle\Tests\Unit\Service\Doctrine\Type;

use AppBundle\Service\Doctrine\Type\DateTimeArrayType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class DateTimeArrayTypeTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        TypeTester::addType(DateTimeArrayType::DATE_TIME_ARRAY, DateTimeArrayType::class);
    }

    public function testEncodesData(): void
    {
        $mockTime = new \DateTime('2016-05-31 15:00:00');

        $type = TypeTester::getType(DateTimeArrayType::DATE_TIME_ARRAY);

        $this->assertJsonStringEqualsJsonString(
            $type->convertToDatabaseValue([$mockTime], $this->getMockForAbstractClass(AbstractPlatform::class)),
            '["2016-05-31 15:00:00"]'
        );
    }

    public function testParsesDBValue(): void
    {
        $string   = '2016-05-31 15:00:00';
        $expected = new \DateTime($string);
        $input    = json_encode([$string]);

        $type   = TypeTester::getType(DateTimeArrayType::DATE_TIME_ARRAY);
        $result = $type->convertToPHPValue($input, $this->getMockForAbstractClass(AbstractPlatform::class));

        $this->assertCount(1, $result);
        $this->assertEquals($result[0], $expected);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^The decode of the JSON string from the database \(\"(.*)\"\) failed due to the following error: "(.*)"\!$/
     */
    public function testCorruptedJSONString(): void
    {
        TypeTester::getType(DateTimeArrayType::DATE_TIME_ARRAY)
            ->convertToPHPValue('["1970-01-01 00:00:00]', $this->getMockForAbstractClass(AbstractPlatform::class));
    }
}

abstract class TypeTester extends Type
{
}
