<?php

declare(strict_types=1);

namespace Tests\Handler\User;

use Domain\User\UserInterface;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class UserRegistrationHandlerTest extends AbstractTest
{
    /**
     * Тест на отображение формы регистрации
     *
     * @throws AppException
     */
    public function testUserRegistrationHandlerCreateForm(): void
    {
        $request = new Request(['REQUEST_URI' => '/registration']);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Регистрация/', $response->getBody());
        self::assertRegExp('/Login/', $response->getBody());
        self::assertRegExp('/Email/', $response->getBody());
        self::assertRegExp('/Password/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда авторизованный пользователь пытается открыть страницу регистрации - его переадресовывает
     * на главную
     *
     * @throws AppException
     */
    public function testUserRegistrationHandlerAlreadyAuth(): void
    {
        $token = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $request = new Request(['REQUEST_URI' => '/registration'], [], [UserInterface::AUTH_TOKEN => $token]);

        $response = $this->app->handle($request);

        self::assertEquals(Response::FOUND, $response->getStatusCode());
    }
}
