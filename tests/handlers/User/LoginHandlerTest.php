<?php

declare(strict_types=1);

namespace Tests\handlers\User;

use Models\User\UserException;
use Models\User\UserInterface;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class LoginHandlerTest extends AbstractTest
{
    /**
     * Тест на успешную авторизацию
     *
     * @throws AppException
     */
    public function testLoginHandlerSuccess(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/login', 'REQUEST_METHOD' => 'POST'],
            ['login' => 'Login-1', 'password' => '12345'],
        );
        $response = $this->app->handle($request);
        $token = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';

        self::assertEquals(Response::FOUND, $response->getStatusCode());
        self::assertEquals([UserInterface::AUTH_TOKEN => $token], $this->app->getContainer()->getCookies()->getArray());
    }

    /**
     * Тест на различные ошибки при авторизации
     *
     * @dataProvider failDataProvider
     * @param string $login
     * @param string $password
     * @param string $error
     * @throws AppException
     */
    public function testLoginHandlerFail(string $login, string $password, string $error): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/login', 'REQUEST_METHOD' => 'POST'],
            ['login' => $login, 'password' => $password],
        );
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/'.$error.'/', $response->getBody());
        self::assertEquals([], $this->app->getContainer()->getCookies()->getArray());
    }

    /**
     * @return array
     */
    public function failDataProvider(): array
    {
        return [
            // Невалидный логин
            [
                'xxx',
                '12345',
                UserException::INVALID_LOGIN_LENGTH,
            ],
            // Неправильный логин
            [
                'Login-',
                '12345',
                UserException::INVALID_LOGIN_OR_PASSWORD,
            ],
            // Неправильный пароль
            [
                'Login-1',
                '123456',
                UserException::INVALID_LOGIN_OR_PASSWORD,
            ],
            // Неправильный логин и пароль
            [
                'Login-11',
                '123456',
                UserException::INVALID_LOGIN_OR_PASSWORD,
            ],
        ];
    }
}
