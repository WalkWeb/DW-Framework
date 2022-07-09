<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Response;

use NW\Response\Response;
use NW\Response\ResponseException;
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
     * @throws ResponseException
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
     * @throws ResponseException
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
     * @throws ResponseException
     */
    public function testResponseSetStatusCodeFail(): void
    {
        $response = new Response();
        $invalidCode = 999;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ResponseException::INCORRECT_STATUS_CODE);

        $response->setStatusCode($invalidCode);
    }

    /**
     * @throws ResponseException
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

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ResponseException::HTTP_HEADER_INCORRECT_TYPE);
        $response->withHeader($header, $value);
    }

    public function testResponseSetInvalidHeaderSymbol(): void
    {
        $response = new Response();

        $header = 'Created By';
        $value = 'WalkWeb';

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ResponseException::HTTP_HEADER_FORBIDDEN_SYMBOLS);
        $response->withHeader($header, $value);
    }

    public function testResponseSetInvalidHeaderValueType(): void
    {
        $response = new Response();

        $header = 'Created-By';
        $value = ['WalkWeb'];

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ResponseException::HEADER_VALUE_INCORRECT_TYPE);
        $response->withHeader($header, $value);
    }

    public function testResponseSetInvalidHeaderValueSymbol(): void
    {
        $response = new Response();

        $header = 'Created-By';
        $value = "\r";

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ResponseException::INCORRECT_HEADER_VALUE);
        $response->withHeader($header, $value);
    }
}
