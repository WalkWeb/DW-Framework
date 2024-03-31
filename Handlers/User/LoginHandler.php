<?php

declare(strict_types=1);

namespace Handlers\User;

use Models\User\DTO\LoginRequestFactory;
use Models\User\UserException;
use Models\User\UserInterface;
use Models\User\UserRepository;
use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class LoginHandler extends AbstractHandler
{
    /**
     * Авторизует пользователя
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            $loginRequest = LoginRequestFactory::create($request->getBody());
            $repository = new UserRepository($this->container);
            if ($token = $repository->auth($loginRequest, KEY)) {
                $this->container->getCookies()->set(UserInterface::AUTH_TOKEN, $token);
                return $this->redirect('/');
            }

            return $this->render('user/login', ['error' => UserException::INVALID_LOGIN_OR_PASSWORD]);

        } catch (AppException $e) {
            return $this->render('user/login', ['error' => $e->getMessage()]);
        }
    }
}
