<?php

declare(strict_types=1);

namespace Handlers\User;

use Models\User\UserFactory;
use Models\User\UserInterface;
use Models\User\UserRepository;
use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class UserCreateHandler extends AbstractHandler
{
    /**
     * Создание нового пользователя на основе данных из формы регистрации
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            $user = UserFactory::createNew($request->getBody(), KEY, 'default');
            $repository = new UserRepository($this->container);
            $repository->add($user);
            $this->container->getCookies()->set(UserInterface::AUTH_TOKEN, $user->getAuthToken());

            return $this->redirect('/');

        } catch (AppException $e) {
            return $this->render('user/registration', ['error' => $e->getMessage()]);
        }
    }
}
