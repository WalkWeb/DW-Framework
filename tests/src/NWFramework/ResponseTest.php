<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\AppException;
use NW\Response;
use NW\Utils\HttpCode;
use Tests\AbstractTestCase;

class ResponseTest extends AbstractTestCase
{
    public function testCreateEmptyResponse(): void
    {
        $response = new Response();

        self::assertEquals('', $response->getBody());
        self::assertEquals(HttpCode::OK, $response->getStatusCode());
        self::assertEquals('OK', $response->getReasonPhase());
        self::assertEquals([], $response->getHeaders());
        self::assertEquals('1.1', $response->getProtocolVersion());
    }

    /**
     * @throws AppException
     */
    public function testCreateResponse(): void
    {
        $body = 'Access denied';

        $response = new Response($body, HttpCode::UNAUTHORIZED);

        self::assertEquals($body, $response->getBody());
        self::assertEquals(HttpCode::UNAUTHORIZED, $response->getStatusCode());
        self::assertEquals('Unauthorized', $response->getReasonPhase());
    }

    /**
     * @throws AppException
     */
    public function testResponseSetStatusCodeSuccess(): void
    {
        $response = new Response();

        self::assertEquals(HttpCode::OK, $response->getStatusCode());
        self::assertEquals('OK', $response->getReasonPhase());

        $response->setStatusCode(HttpCode::NOT_FOUND);

        self::assertEquals(HttpCode::NOT_FOUND, $response->getStatusCode());
        self::assertEquals('Not Found', $response->getReasonPhase());
    }

    /**
     * @throws AppException
     */
    public function testResponseSetStatusCodeFail(): void
    {
        $response = new Response();
        $invalidCode = 999;

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Указан некорректный код ответа');

        $response->setStatusCode($invalidCode);
    }

    /**
     * @throws AppException
     */
    public function testResponseSetHeaderSuccess(): void
    {
        $response = new Response();

        self::assertEquals([], $response->getHeaders());

        $header = 'Created-By';
        $value = 'WalkWeb';

        $response->withHeader($header, $value);

        self::assertEquals([$header => $value], $response->getHeaders());
    }

    public function testResponseSetHeaderInvalidHeaderType(): void
    {
        $response = new Response();

        $header = ['Created-By'];
        $value = 'WalkWeb';

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('HTTP заголовок должен быть строкой');
        $response->withHeader($header, $value);
    }

    public function testResponseSetInvalidHeaderSymbol(): void
    {
        $response = new Response();

        $header = 'Created By';
        $value = 'WalkWeb';

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Недопустимые символы в HTTP заголовке');
        $response->withHeader($header, $value);
    }

    public function testResponseSetInvalidHeaderValueType(): void
    {
        $response = new Response();

        $header = 'Created-By';
        $value = ['WalkWeb'];

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Значение HTTP заголовка может быть только строкой или числом');
        $response->withHeader($header, $value);
    }

    public function testResponseSetInvalidHeaderValueSymbol(): void
    {
        $response = new Response();

        $header = 'Created-By';
        $value = "\r";

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Некорректный формат значения HTTP заголовка');
        $response->withHeader($header, $value);
    }
}
