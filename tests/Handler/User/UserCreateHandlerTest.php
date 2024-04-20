<?php

declare(strict_types=1);

namespace Tests\Handler\User;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class UserCreateHandlerTest extends AbstractTest
{
    /**
     * Тест на успешную регистрацию нового пользователя
     *
     * @throws AppException
     */
    public function testUserCreateHandlerSuccess(): void
    {
        $connection = $this->app->getContainer()->getConnectionPool()->getConnection();
        $connection->autocommit(false);
        $login = 'User-1';
        $email = 'mail@mail.com';
        $password = '12345';

        $request = new Request(
            ['REQUEST_URI' => '/registration', 'REQUEST_METHOD' => 'POST'],
            ['login' => $login, 'email' => $email, 'password' => $password],
        );

        $response = $this->app->handle($request);

        self::assertEquals(Response::FOUND, $response->getStatusCode());

        $data = $connection->query(
            'SELECT * FROM `users` WHERE `login` = ?',
            [['type' => 's', 'value' => $login]],
            true
        );

        self::assertEquals($data['login'], $login);
        self::assertEquals($data['email'], $email);

        $connection->rollback();
    }

    /**
     * Тест на различные ошибки при регистрации нового пользователя
     *
     * @dataProvider failDataProvider
     * @param string $login
     * @param string $email
     * @param string $error
     * @param bool $existUser
     * @throws AppException
     */
    public function testUserCreateHandlerFail(string $login, string $email, string $error, bool $existUser): void
    {
        $connection = $this->app->getContainer()->getConnectionPool()->getConnection();
        $connection->autocommit(false);

        $request = new Request(
            ['REQUEST_URI' => '/registration', 'REQUEST_METHOD' => 'POST'],
            ['login' => $login, 'email' => $email, 'password' => '12345'],
        );

        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp("/$error/", $response->getBody());

        if (!$existUser) {
            $data = $connection->query(
                'SELECT * FROM `users` WHERE `login` = ?',
                [['type' => 's', 'value' => $login]],
                true
            );

            self::assertEquals([], $data);
        }

        $connection->rollback();
    }

    public function failDataProvider(): array
    {
        return [
            // Тест на ошибку валидации
            [
                'xxx',
                'mymail@mail.ru',
                'Incorrect parameter "login", should be min-max length: 4-14',
                false,
            ],
            // Пользователь с таким логином уже существует
            [
                'Login-1',
                'mymail@mail.ru',
                'User with this login already exists',
                true,
            ],
            // Пользователь с такой почтой уже существует
            [
                'Login',
                'mail1@mail.com',
                'User with this email already exists',
                true,
            ],
        ];
    }
}
