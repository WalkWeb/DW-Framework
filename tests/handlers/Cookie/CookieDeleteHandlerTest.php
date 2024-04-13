<?php

declare(strict_types=1);

namespace Tests\handlers\Cookie;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class CookieDeleteHandlerTest extends AbstractTest
{
    /**
     * Тест на удаление кука
     *
     * @throws AppException
     */
    public function testCookieDeleteHandler(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/cookies/delete', 'REQUEST_METHOD' => 'POST'],
            ['name' => 'xxx'], // куки, которые удаляем
            ['xxx' => 'xxx'],  // куки, которые существуют
        );
        $response = $this->app->handle($request);

        // В случае успеха делается редирект
        self::assertEquals(Response::FOUND, $response->getStatusCode());
        // Куки стали пустыми
        self::assertEquals([], $this->app->getContainer()->getCookies()->getArray());
    }
}
