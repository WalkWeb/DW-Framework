<?php

declare(strict_types=1);

namespace Handlers\User;

use Models\User\UserInterface;
use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class LogoutHandler extends AbstractHandler
{
    /**
     * Разлогинивает пользователя (удаляет авторизационный токен из куков)
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        $this->container->getCookies()->delete(UserInterface::AUTH_TOKEN);
        return $this->redirect('/');
    }
}
