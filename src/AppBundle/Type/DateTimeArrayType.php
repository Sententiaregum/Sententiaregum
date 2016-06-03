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

namespace AppBundle\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Custom DBAL type which formats a datetime object into the database's datetime (normally "Y-m-d H:i:s") format
 * and serializes the whole dataset into a JSON array.
 *
 * Normally the array `type` will be used and the whole structure will be serialized and can't be parsed
 * by any other service and it's more difficult to read and maintain the structure,
 * so a simple JSON array will be used which improves readability.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
final class DateTimeArrayType extends Type
{
    const DATE_TIME_ARRAY = 'date_time_array';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::DATE_TIME_ARRAY;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $format = $platform->getDateTimeFormatString();

        return json_encode(
            array_map(function (\DateTime $dateTime) use ($format) {
                return $dateTime->format($format);
            }, $value)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If the data decode fails.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $raw = json_decode($value, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(sprintf(
                'The decode of the JSON string from the database ("%s") failed due to the following error: "%s"!',
                $value,
                json_last_error_msg()
            ));
        }

        $dbFormat = $platform->getDateTimeFormatString();
        return array_map(
            function ($format) use ($dbFormat) {
                return \DateTime::createFromFormat($dbFormat, $format);
            },
            $raw
        );
    }
}
