<?php

declare(strict_types=1);

namespace Tests\Handler\Cookie;

use Exception;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use Tests\AbstractTest;

class CookieAddHandlerTest extends AbstractTest
{
    /**
     * Тест на успешное добавление кука
     *
     * @throws AppException
     */
    public function testCookieAddHandlerSuccess(): void
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

    /**
     * @dataProvider failDataProvider
     * @param array $data
     * @param string $error
     * @throws AppException
     */
    public function testCookieAddHandlerFail(array $data, string $error): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/cookies/add', 'REQUEST_METHOD' => 'POST'],
            $data,
        );
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp("/$error/", $response->getBody());

        // Куки не появились
        self::assertEquals([], $this->app->getContainer()->getCookies()->getArray());
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
                [
                    'value' => 'value',
                ],
                'Error: Bad request: "name" value required and expected string',
            ],
            // name некорректного типа
            [
                [
                    'name'  => 123,
                    'value' => 'value',
                ],
                'Error: Bad request: "name" value required and expected string',
            ],
            // name пустая строка
            [
                [
                    'name'  => '',
                    'value' => 'value',
                ],
                'Length "name" parameter must be from 1 to 50 characters',
            ],
            // name больше максимальной длинны
            [
                [
                    'name'  => self::generateString(51),
                    'value' => 'value',
                ],
                'Length "name" parameter must be from 1 to 50 characters',
            ],
            // Отсутствует value
            [
                [
                    'name' => 'name',
                ],
                'Error: Bad request: "value" value required and expected string',
            ],
            // value некорректного типа
            [
                [
                    'name' => 'name',
                    'value' => null,
                ],
                'Error: Bad request: "value" value required and expected string',
            ],
            // value пустая строка
            [
                [
                    'name' => 'name',
                    'value' => '',
                ],
                'Length "value" parameter must be from 1 to 100 characters',
            ],
            // value больше максимальной длинны
            [
                [
                    'name'  => 'name',
                    'value' => self::generateString(101),
                ],
                'Length "value" parameter must be from 1 to 100 characters',
            ],
        ];
    }
}
