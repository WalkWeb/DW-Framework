<?php

declare(strict_types=1);

namespace Tests\handlers\User;

use Models\User\UserInterface;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class VerifiedEmailHandlerTest extends AbstractTest
{
    /**
     * Тест на успешное отображение страницы о необходимости подтвердить email-адрес
     */
    public function testNotVerifiedEmailHandlerSuccess(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $request = new Request(['REQUEST_URI' => '/verified_email'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Подтвердите ваш email/', $response->getBody());
        self::assertRegExp('/Вам необходимо подтвердить ваш email/', $response->getBody());

    }

    /**
     * Тест на ситуацию, когда неавторизованный пользователь пытается открыть страницу - ему сообщается, нужно
     * авторизоваться
     *
     * @throws AppException
     */
    public function testNotVerifiedEmailHandlerUnauthorized(): void
    {
        $request = new Request(['REQUEST_URI' => '/verified_email']);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Необходима авторизация/', $response->getBody());
        self::assertRegExp('/Перейти на страницу авторизации/', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда пользователь с подтвержденной почтой пытается открыть страницу - ему сообщается что все ок
     *
     * @throws AppException
     */
    public function testNotVerifiedEmailHandlerAlreadyVerified(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFGyyyy';
        $request = new Request(['REQUEST_URI' => '/verified_email'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Email успешно подтвержден/', $response->getBody());
        self::assertRegExp('/Вы успешно подтвердили email, ваш аккаунт активирован и все возможности доступны/', $response->getBody());
    }
}
