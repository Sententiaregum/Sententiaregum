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

namespace AppBundle\Tests\Unit\EventListener;

use AppBundle\EventListener\I18nSecurityResponseListener;
use Ma27\ApiKeyAuthenticationBundle\Event\AssembleResponseEvent;
use Ma27\ApiKeyAuthenticationBundle\Exception\CredentialException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;

class I18nSecurityResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideMessage
     *
     * @param string $message
     * @param string $expected
     */
    public function testBuildResponse($message, string $expected): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with($expected)
            ->willReturnArgument(0);
        $locales = ['en'];

        if ($message) {
            $exception = new CredentialException($message);
        } else {
            $exception = new CredentialException();
        }
        $listener = new I18nSecurityResponseListener($locales, $translator);
        $event    = new AssembleResponseEvent(null, $exception);

        $listener->onResponseCreation($event);
        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertSame($data['message']['en'], $expected);
        $this->assertTrue($event->isPropagationStopped());
    }

    public function testSuccessResponse(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->never())
            ->method('trans');

        $locales = ['en'];

        $listener = new I18nSecurityResponseListener($locales, $translator);
        $event    = new AssembleResponseEvent(null, null);

        $listener->onResponseCreation($event);
        $response = $event->getResponse();
        $this->assertNull($response);
    }

    /**
     * @return array
     */
    public function provideMessage(): array
    {
        return [
            [null, 'BACKEND_AUTH_FAILURE'],
            ['A custom message', 'A custom message'],
        ];
    }
}
