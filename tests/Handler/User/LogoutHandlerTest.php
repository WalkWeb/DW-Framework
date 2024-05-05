<?php

declare(strict_types=1);

namespace Tests\Handler\User;

use Domain\User\UserInterface;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use Tests\AbstractTest;

class LogoutHandlerTest extends AbstractTest
{
    /**
     * Тест на разлогинивание пользователя (удаление авторизационного токена + переадресация на главную)
     *
     * @throws AppException
     */
    public function testLogoutHandler(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFGyyyy';
        $request = new Request(['REQUEST_URI' => '/logout'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::FOUND, $response->getStatusCode());
        self::assertEquals([], $this->app->getContainer()->getCookies()->getArray());
    }
}
