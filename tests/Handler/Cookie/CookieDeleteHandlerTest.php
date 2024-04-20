<?php

declare(strict_types=1);

namespace Tests\Handler\Cookie;

use Exception;
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


    /**
     * Тесты на различные варианты невалидных данных
     *
     * @dataProvider failDataProvider
     * @param array $data
     * @param string $error
     * @throws AppException
     */
    public function testCookieDeleteHandlerFail(array $data, string $error): void
    {
        $cookie = ['xxx' => 'xxx'];
        $request = new Request(
            ['REQUEST_URI' => '/cookies/delete', 'REQUEST_METHOD' => 'POST'],
            $data,
            $cookie,
        );
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp("/$error/", $response->getBody());

        // Проверяем, что куки не исчезли
        self::assertEquals($cookie, $this->app->getContainer()->getCookies()->getArray());
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function failDataProvider(): array
    {
        return [
            // Отсутствует name
            [
                [],
                'Error: Bad request: "name" value required and expected string',
            ],
            // name некорректного типа
            [
                [
                    'name'  => 123,
                ],
                'Error: Bad request: "name" value required and expected string',
            ],
            // name пустая строка
            [
                [
                    'name'  => '',
                ],
                'Length "name" parameter must be from 1 to 50 characters',
            ],
            // name больше максимальной длинны
            [
                [
                    'name'  => self::generateString(51),
                ],
                'Length "name" parameter must be from 1 to 50 characters',
            ],
        ];
    }
}
