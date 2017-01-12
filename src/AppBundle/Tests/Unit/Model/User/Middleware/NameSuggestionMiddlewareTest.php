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

namespace AppBundle\Tests\Unit\Model\User\Middleware;

use AppBundle\Model\User\DTO\CreateUserDTO;
use AppBundle\Model\User\Middleware\NameSuggestionMiddleware;
use AppBundle\Model\User\Util\NameSuggestion\Suggestor\SuggestorInterface;
use AppBundle\Validator\Constraints\UniqueProperty;
use AppBundle\Validator\Middleware\ValidationInfo;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class NameSuggestionMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachSuggestions(): void
    {
        $suggestions = ['Ma27_2016', 'Ma27_2000'];

        $suggestor = $this->createMock(SuggestorInterface::class);
        $suggestor
            ->expects($this->any())
            ->method('getPossibleSuggestions')
            ->with('Ma27')
            ->willReturn($suggestions);

        $middleware    = new NameSuggestionMiddleware($suggestor);
        $dto           = new CreateUserDTO();
        $dto->username = 'Ma27';

        $info                = new ValidationInfo();
        $info->violationList = new ConstraintViolationList([
            new ConstraintViolation('Non-unique username', 'Non-unique username!', [], 'root', 'username', 'Ma27', null, UniqueProperty::NON_UNIQUE_PROPERTY),
        ]);

        $dto->setValidationInfo($info);

        $invoked = false;
        $next    = function () use (&$invoked) {
            $invoked = true;
        };

        $middleware->handle($dto, $next);

        $this->assertFalse($invoked);
        $this->assertSame($suggestions, $dto->getInfo()->getExtraValue(CreateUserDTO::SUGGESTIONS));
    }

    /**
     * @dataProvider provideAbortCases
     *
     * @param object $dto
     */
    public function testAborted($dto): void
    {
        $suggestor  = $this->createMock(SuggestorInterface::class);
        $middleware = new NameSuggestionMiddleware($suggestor);

        $invoked = false;
        $next    = function () use (&$invoked) {
            $invoked = true;
        };

        $middleware->handle($dto, $next);

        $this->assertTrue($invoked);
    }

    /**
     * @return array
     */
    public function provideAbortCases(): array
    {
        $dto = new CreateUserDTO();
        $dto->setValidationInfo(new ValidationInfo());

        return [
            [
                new \stdClass(),
            ],
            [
                $dto,
            ],
        ];
    }
}
