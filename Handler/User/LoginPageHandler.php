<?php

declare(strict_types=1);

namespace Handler\User;

use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

class LoginPageHandler extends AbstractHandler
{
    public const ALREADY_AUTH = 'Вы уже авторизованы';

    /**
     * Отображает страницу для авторизации
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        if ($this->container->exist('user')) {
            return $this->render('user/login', [
                'error'     => self::ALREADY_AUTH,
                'csrfToken' => $this->container->getCsrf()->getCsrfToken(),
            ]);
        }

        return $this->render('user/login', [
            'csrfToken' => $this->container->getCsrf()->getCsrfToken(),
        ]);
    }
}
