<?php

declare(strict_types=1);

namespace Tests\Handler\Cookie;

use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use Tests\AbstractTest;

class CookieGetListHandlerTest extends AbstractTest
{
    /**
     * Тест на отображение страницы с куками
     *
     * @throws AppException
     */
    public function testCookieGetListHandler(): void
    {
        $request = new Request(['REQUEST_URI' => '/cookies']);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Cookies/', $response->getBody());
        self::assertRegExp('/Cookies отсутствуют/', $response->getBody());
    }
}
