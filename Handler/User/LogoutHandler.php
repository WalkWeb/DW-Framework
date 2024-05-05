<?php

declare(strict_types=1);

namespace Handler\User;

use Domain\User\UserInterface;
use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

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
