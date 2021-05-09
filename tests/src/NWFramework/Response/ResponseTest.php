<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Response;

use NW\Response\Response;
use NW\Response\ResponseException;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testCreateEmptyResponse(): void
    {
        $response = new Response();

        self::assertEquals('', $response->getBody());
        self::assertEquals(200, $response->getStatusCode());
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
        $code = 401;

        $response = new Response($body, $code);

        self::assertEquals($body, $response->getBody());
        self::assertEquals($code, $response->getStatusCode());
        self::assertEquals('Unauthorized', $response->getReasonPhase());
    }

    /**
     * @throws ResponseException
     */
    public function testResponseSetStatusCodeSuccess(): void
    {
        $response = new Response();
        $defaultCode = 200;
        $newCode = 404;

        self::assertEquals($defaultCode, $response->getStatusCode());
        self::assertEquals('OK', $response->getReasonPhase());

        $response->setStatusCode($newCode);

        self::assertEquals($newCode, $response->getStatusCode());
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
