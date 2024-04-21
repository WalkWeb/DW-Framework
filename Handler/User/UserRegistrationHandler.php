<?php

declare(strict_types=1);

namespace Handler\User;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

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
