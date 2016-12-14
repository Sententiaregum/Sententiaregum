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

namespace AppBundle\Tests\Unit\Validator\Middleware;

use AppBundle\Validator\Middleware\DTOValidationMiddleware;
use AppBundle\Validator\Middleware\ValidatableDTO;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOValidationMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideConfiguredObjects
     *
     * @param object $message
     */
    public function testNoValidatableDTOGiven($message): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->never())
            ->method('validate');

        $middleware = new DTOValidationMiddleware($validator);

        $that    = &$this;
        $invoked = false;
        $next    = function ($nextMessage) use ($that, &$invoked, $message) {
            $that->assertSame($nextMessage, $message);
            $invoked = true;
        };

        $middleware->handle($message, $next);
        $this->assertTrue($invoked);
    }

    public function testValidationWithNoViolations(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $middleware = new DTOValidationMiddleware($validator);

        $dto     = new TestDTO();
        $that    = &$this;
        $invoked = false;

        /** @var ValidatableDTO $nextMessage */
        $next = function ($nextMessage) use ($that, &$invoked, $dto) {
            $that->assertSame($nextMessage, $dto);
            $that->assertTrue($nextMessage->getInfo()->isValid());
            $that->assertCount(0, $nextMessage->getInfo()->violationList);
            $invoked = true;
        };

        $middleware->handle($dto, $next);
        $this->assertTrue($invoked);
    }

    public function testValidationWithViolations(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation('Error!', '<error template>', [], 'root', 'root.property', 'foobar'),
            ]));

        $middleware = new DTOValidationMiddleware($validator);

        $dto     = new TestDTO();
        $invoked = false;

        $next = function () use (&$invoked) {
            $invoked = true;
        };

        $middleware->handle($dto, $next);
        $this->assertFalse($invoked);

        $this->assertFalse($dto->getInfo()->isValid());
    }

    public function testValidationWithViolationsAndEnabledContinuing(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation('Error!', '<error template>', [], 'root', 'root.property', 'foobar'),
            ]));

        $middleware = new DTOValidationMiddleware($validator);

        $dto     = new TestDTOWithContinuingAfterFailedValidation();
        $invoked = false;

        $next = function () use (&$invoked) {
            $invoked = true;
        };

        $middleware->handle($dto, $next);
        $this->assertTrue($invoked);

        $this->assertFalse($dto->getInfo()->isValid());
    }

    /**
     * @return array
     */
    public function provideConfiguredObjects(): array
    {
        $mock = $this->createMock(ValidatableDTO::class);
        $mock
            ->expects($this->any())
            ->method('shouldValidate')
            ->willReturn(false);

        return [
            [new \stdClass()],
            [$mock],
        ];
    }
}

class TestDTO extends ValidatableDTO
{
}

class TestDTOWithContinuingAfterFailedValidation extends ValidatableDTO
{
    public function __construct()
    {
        $this->continueOnInvalid = true;
    }
}
