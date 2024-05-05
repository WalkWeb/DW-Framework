<?php

declare(strict_types=1);

namespace Handler\User;

use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

class UserRegistrationHandler extends AbstractHandler
{
    /**
     * Страница регистрации
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        if ($this->container->exist('user')) {
            return $this->redirect('/');
        }

        return $this->render('user/registration', ['csrfToken' => $this->container->getCsrf()->getCsrfToken()]);
    }
}
