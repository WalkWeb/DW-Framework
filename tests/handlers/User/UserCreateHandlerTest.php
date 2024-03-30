<?php

declare(strict_types=1);

namespace Tests\handlers\User;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class UserCreateHandlerTest extends AbstractTestCase
{
    /**
     * @throws AppException
     */
    public function testUserCreateHandlerSuccess(): void
    {
        $connection = $this->app->getContainer()->getConnection();
        $connection->autocommit(false);
        $login = 'User-1';
        $email = 'mail@mail.com';
        $password = '12345';

        $request = new Request(
            ['REQUEST_URI' => '/registration', 'REQUEST_METHOD' => 'POST'],
            ['login' => $login, 'email' => $email, 'password' => $password],
        );

        $response = $this->app->handle($request);

        self::assertEquals(Response::MOVED_PERMANENTLY, $response->getStatusCode());

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
     * @throws AppException
     */
    public function testUserCreateHandlerFail(): void
    {
        $connection = $this->app->getContainer()->getConnection();
        $connection->autocommit(false);
        $login = 'xxx';

        $request = new Request(
            ['REQUEST_URI' => '/registration', 'REQUEST_METHOD' => 'POST'],
            ['login' => $login, 'email' => 'mail@mail.com', 'password' => '12345'],
        );

        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Incorrect parameter "login", should be min-max length: 4-14/', $response->getBody());

        $data = $connection->query(
            'SELECT * FROM `users` WHERE `login` = ?',
            [['type' => 's', 'value' => $login]],
            true
        );

        self::assertEquals([], $data);

        $connection->rollback();
    }
}
