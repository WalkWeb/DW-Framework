<?php

declare(strict_types=1);

namespace Handlers\User;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class NotVerifiedEmailHandler extends AbstractHandler
{
    /**
     * Отображает страницу с необходимостью подтвердить email
     *
     * Если страницу пытается открыть неавторизованный пользователь - его переадресовывает на главную
     *
     * Если пользователь уже имеет подтвержденный email - переадресовывает на главную
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        if (!$this->container->exist('user')) {
            return $this->redirect('/');
        }

        if ($this->container->getUser()->isEmailVerified()) {
            return $this->redirect('/');
        }

        return $this->render('user/email_not_verified');
    }
}
