<?php

declare(strict_types=1);

namespace Tests\handlers\User;

use Models\User\UserInterface;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class LogoutHandlerTest extends AbstractTestCase
{
    /**
     * Тест на разлогинивание пользователя (удаление авторизационного токена + переадресация на главную)
     *
     * @throws AppException
     */
    public function testLogoutHandler(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG4xxx';
        $request = new Request(['REQUEST_URI' => '/logout'], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertEquals([], $this->app->getContainer()->getCookies()->getArray());
    }
}
