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

namespace AppBundle\Tests\Unit\Validator\Middleware;

use AppBundle\Validator\Middleware\ValidatableDTO;
use AppBundle\Validator\Middleware\ValidationInfo;

class ValidatableDTOTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults(): void
    {
        /** @var ValidatableDTO $dto */
        $dto = $this->getMockForAbstractClass(ValidatableDTO::class);

        $this->assertTrue($dto->shouldValidate());
        $this->assertFalse($dto->shouldContinueOnInvalid());
        $this->assertNull($dto->getInfo());
    }

    public function testAddValidationInfo(): void
    {
        /** @var ValidatableDTO $dto */
        $dto = $this->getMockForAbstractClass(ValidatableDTO::class);

        $info = new ValidationInfo();
        $dto->setValidationInfo($info);

        $this->assertSame($dto->getInfo(), $info);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot override the whole validation info! Please use the getter and modify the reference!
     */
    public function testOverrideValidationInfo(): void
    {
        /** @var ValidatableDTO $dto */
        $dto = $this->getMockForAbstractClass(ValidatableDTO::class);

        $dto->setValidationInfo(new ValidationInfo());
        $dto->setValidationInfo(new ValidationInfo());
    }
}
