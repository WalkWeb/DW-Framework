<?php

declare(strict_types=1);

namespace Tests\handlers\Post;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class PostGetHandlerTest extends AbstractTest
{
    /**
     * Тест на получения конкретного поста
     *
     * @throws AppException
     */
    public function testPostGetSuccess(): void
    {
        $request = new Request(['REQUEST_URI' => '/post/post-3']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Пост 3/', $response->getBody());
        self::assertRegExp('/Содержимое третьего поста/', $response->getBody());
        self::assertRegExp('/Вернуться к списку постов/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    /**
     * Тест на ошибки получения конкретного поста
     *
     * @dataProvider failDataProvider
     * @param string $uri
     * @throws AppException
     */
    public function testPostGetFail(string $uri): void
    {
        $request = new Request(['REQUEST_URI' => $uri]);
        $response = $this->app->handle($request);

        self::assertRegExp('/Ошибка 404: Страница не найдена/', $response->getBody());
        self::assertEquals(Response::NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @return array
     */
    public function failDataProvider(): array
    {
        return [
            // не указан id поста
            [
                '/post/',
            ],
            // id не int
            [
                '/post/abc',
            ],
        ];
    }
}
