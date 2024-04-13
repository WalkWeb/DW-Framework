<?php

declare(strict_types=1);

namespace Tests\handlers\Cookie;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class CookieAddHandlerTest extends AbstractTest
{
    /**
     * Тест на добавление кука
     *
     * @throws AppException
     */
    public function testCookieAddHandler(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/cookies/add', 'REQUEST_METHOD' => 'POST'],
            ['name' => 'name', 'value' => 'value'],
        );
        $response = $this->app->handle($request);

        // В случае успеха делается редирект
        self::assertEquals(Response::FOUND, $response->getStatusCode());
        // Куки появились
        self::assertEquals(['name' => 'value'], $this->app->getContainer()->getCookies()->getArray());
    }
}
