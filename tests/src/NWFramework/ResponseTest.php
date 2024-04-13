<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\AppException;
use NW\Response;
use Tests\AbstractTest;

class ResponseTest extends AbstractTest
{
    /**
     * Тест на создание Response с параметрами по умолчанию
     */
    public function testResponseCreateDefault(): void
    {
        $response = new Response();

        self::assertEquals('', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertEquals('OK', $response->getReasonPhase());
        self::assertEquals([], $response->getHeaders());
        self::assertEquals('1.1', $response->getProtocolVersion());
    }

    /**
     * Тест на создание Response с пользовательскими данными
     *
     * @throws AppException
     */
    public function testResponseCreate(): void
    {
        $body = 'Access denied';
        $code = Response::UNAUTHORIZED;

        $response = new Response($body, $code);

        self::assertEquals($body, $response->getBody());
        self::assertEquals($code, $response->getStatusCode());
        self::assertEquals('Unauthorized', $response->getReasonPhase());
    }

    /**
     * Тест на установку нового статуса ответа
     *
     * @throws AppException
     */
    public function testResponseSetStatusCodeSuccess(): void
    {
        $response = new Response();

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertEquals('OK', $response->getReasonPhase());

        $response->setStatusCode(Response::NOT_FOUND);

        self::assertEquals(Response::NOT_FOUND, $response->getStatusCode());
        self::assertEquals('Not Found', $response->getReasonPhase());
    }

    /**
     * Тест на установку неизвестного статуса ответа
     *
     * @throws AppException
     */
    public function testResponseSetStatusCodeFail(): void
    {
        $response = new Response();
        $invalidCode = 999;

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(Response::ERROR_INVALID_CODE);

        $response->setStatusCode($invalidCode);
    }

    /**
     * Тест на установку заголовка в Response
     *
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

    /**
     * Тест на попытку установить некорректный ключ заголовка
     *
     * @throws AppException
     */
    public function testResponseSetHeaderInvalidHeaderType(): void
    {
        $response = new Response();

        $header = ['Created-By'];
        $value = 'WalkWeb';

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(Response::ERROR_INVALID_HTTP_HEADER);
        $response->withHeader($header, $value);
    }

    /**
     * Тест на ситуацию, когда в ключе заголовка недопустимый символ (пробел)
     *
     * @throws AppException
     */
    public function testResponseSetInvalidHeaderSymbol(): void
    {
        $response = new Response();

        $header = 'Created By';
        $value = 'WalkWeb';

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(Response::ERROR_INVALID_HEADER_CHARS);
        $response->withHeader($header, $value);
    }

    /**
     * Тест на ситуацию, когда пытаются установить некорректное значение заголовка
     *
     * @throws AppException
     */
    public function testResponseSetInvalidHeaderValueType(): void
    {
        $response = new Response();

        $header = 'Created-By';
        $value = ['WalkWeb'];

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(Response::ERROR_INVALID_HEADER_VALUE);
        $response->withHeader($header, $value);
    }

    /**
     * Тест на ситуацию, когда значение заголовка содержит недопустимые символы
     *
     * @throws AppException
     */
    public function testResponseSetInvalidHeaderValueSymbol(): void
    {
        $response = new Response();

        $header = 'Created-By';
        $value = "\r";

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(Response::ERROR_INVALID_HEADER_VALUE);
        $response->withHeader($header, $value);
    }

    /**
     * Тест на установку нового тела ответа
     *
     * @throws AppException
     */
    public function testResponseSetBody(): void
    {
        $body = 'body';
        $newBody = 'new_body';

        $response = new Response($body, Response::OK);

        self::assertEquals($body, $response->getBody());

        $response->setBody($newBody);

        self::assertEquals($newBody, $response->getBody());
    }
}
