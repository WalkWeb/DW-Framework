<?php

declare(strict_types=1);

namespace Tests\handlers\User;

use Models\User\UserInterface;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class UserProfileHandlerTest extends AbstractTestCase
{
    /**
     * Тест на ситуацию, когда открывается страница профиля без авторизационного токена
     *
     * @throws AppException
     */
    public function testUserProfileHandlerNotAuth(): void
    {
        $request = new Request(['REQUEST_URI' => '/profile']);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Профиль/', $response->getBody());
        self::assertRegExp('/Вы не авторизованны/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда открывается страница профиля с неизвестным авторизационным токеном
     *
     * @throws AppException
     */
    public function testUserProfileHandlerAuthUnknownAuthToken(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG4xxx';
        $request = new Request(['REQUEST_URI' => '/profile'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Профиль/', $response->getBody());
        self::assertRegExp('/Вы не авторизованны/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда открывается страница профиля с существующим авторизационным токеном
     *
     * @throws AppException
     */
    public function testUserProfileHandlerAuth(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $request = new Request(['REQUEST_URI' => '/profile'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/23388e70-7171-4f14-bf13-39c1d77861bb/', $response->getBody());
        self::assertRegExp('/Login-1/', $response->getBody());
        self::assertRegExp('/mail1@mail.com/', $response->getBody());
        self::assertRegExp('/2024-03-30 20:59:50/', $response->getBody());
    }
}
