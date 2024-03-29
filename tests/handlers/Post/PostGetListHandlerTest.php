<?php

declare(strict_types=1);

namespace Tests\handlers\Post;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class PostGetListHandlerTest extends AbstractTestCase
{
    /**
     * Тест на получения списка постов
     *
     * @throws AppException
     */
    public function testPostGetListSuccess(): void
    {
        $request = new Request(['REQUEST_URI' => '/posts/1']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Посты/', $response->getBody());
        self::assertRegExp('/Пост 1/', $response->getBody());
        self::assertRegExp('/Содержимое первого поста/', $response->getBody());
        self::assertRegExp('/Пост 5/', $response->getBody());
        self::assertRegExp('/Содержимое пятого поста/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());

        $request = new Request(['REQUEST_URI' => '/posts/2']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Посты/', $response->getBody());
        self::assertRegExp('/Пост 6/', $response->getBody());
        self::assertRegExp('/Содержимое шестого поста/', $response->getBody());
        self::assertRegExp('/Пост 10/', $response->getBody());
        self::assertRegExp('/Содержимое десятого поста/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());

        $request = new Request(['REQUEST_URI' => '/posts/3']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Посты/', $response->getBody());
        self::assertRegExp('/Пост 11/', $response->getBody());
        self::assertRegExp('/Содержимое одиннадцатого поста/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    /**
     * Тест на ошибку получения списка постов - не указана страница
     *
     * @dataProvider failDataProvider
     * @param string $uri
     * @throws AppException
     */
    public function testPostGetListNotFound(string $uri): void
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
            // не указана страница
            [
                '/posts/',
            ],
            // страница не int
            [
                '/posts/abc',
            ],
            // страница на которой нет постов
            [
                '/posts/4',
            ],
        ];
    }
}
