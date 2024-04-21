<?php

declare(strict_types=1);

namespace Tests\Handler\User;

use Handler\User\LoginPageHandler;
use Domain\User\UserInterface;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class LoginPageHandlerTest extends AbstractTest
{
    /**
     * Тест на отображение формы авторизации
     *
     * @throws AppException
     */
    public function testLoginPageHandlerGetForm(): void
    {
        $request = new Request(['REQUEST_URI' => '/login']);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Вход/', $response->getBody());
        self::assertRegExp('/Login/', $response->getBody());
        self::assertRegExp('/Password/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда уже авторизованный пользователь открывает страницу авторизации
     *
     * @throws AppException
     */
    public function testLoginPageHandlerAlreadyAuth(): void
    {
        $token = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $request = new Request(['REQUEST_URI' => '/login'], [], [UserInterface::AUTH_TOKEN => $token]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/' . LoginPageHandler::ALREADY_AUTH . '/', $response->getBody());
    }
}
