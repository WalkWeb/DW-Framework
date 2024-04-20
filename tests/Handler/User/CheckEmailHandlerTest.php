<?php

declare(strict_types=1);

namespace Tests\Handler\User;

use Domain\User\UserInterface;
use NW\App;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class CheckEmailHandlerTest extends AbstractTest
{
    /**
     * Тест на успешное подтверждение email
     *
     * @throws AppException
     */
    public function testCheckEmailHandlerSuccess(): void
    {
        $container = $this->getContainer();
        $container->getConnectionPool()->getConnection()->autocommit(false);
        $router = require __DIR__ . '/../../../routes/web.php';
        $app = new App($router, $container);
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $verifiedToken = 'ISUgTBiTjVht2PIVQqSR52hmeXNs2Z';
        $request = new Request(['REQUEST_URI' => "/check_email/$verifiedToken"], [], [UserInterface::AUTH_TOKEN => $authToken]);

        // Проверяем данные в базе перед вызовом метода
        $data = $container->getConnectionPool()->getConnection()->query(
            'SELECT * FROM `users` WHERE `auth_token` = ?',
            [['type' => 's', 'value' => $authToken]],
            true
        );

        self::assertEquals(0, $data['email_verified']);
        self::assertEquals(0, $data['reg_complete']);

        $response = $app->handle($request);

        // В случае успеха происходит переадресация
        self::assertEquals(Response::FOUND, $response->getStatusCode());

        // Проверяем данные в базе после вызова метода
        $data = $container->getConnectionPool()->getConnection()->query(
            'SELECT * FROM `users` WHERE `auth_token` = ?',
            [['type' => 's', 'value' => $authToken]],
            true
        );

        self::assertEquals(1, $data['email_verified']);
        self::assertEquals(1, $data['reg_complete']);

        $container->getConnectionPool()->getConnection()->rollback();
    }

    /**
     * Тест на ситуацию, когда к методу обращается неавторизованный пользователь
     *
     * @throws AppException
     */
    public function testCheckEmailHandlerUnauthorized(): void
    {
        $verifiedToken = 'ISUgTBiTjVht2PIVQqSR52hmeXNs2Z';
        $request = new Request(['REQUEST_URI' => "/check_email/$verifiedToken"]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Необходима авторизация/', $response->getBody());
        self::assertRegExp('/Перейти на страницу авторизации/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда указан некорректный токен подтверждения email
     *
     * @throws AppException
     */
    public function testCheckEmailHandlerInvalidToken(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $request = new Request(['REQUEST_URI' => '/check_email/xxxxxxxx'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Ошибка подтверждения email/', $response->getBody());
        self::assertRegExp('/Указан некорректный токен подтверждения email/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда к методу обращается пользовать с уже подтвержденным email - просто переадресация
     *
     * @throws AppException
     */
    public function testCheckEmailHandlerAlreadyCheck(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFGyyyy';
        $verifiedToken = 'ISUgTBiTjVht2PIVQqSR52hmeXNxxx';
        $request = new Request(['REQUEST_URI' => "/check_email/$verifiedToken"], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::FOUND, $response->getStatusCode());
    }
}
