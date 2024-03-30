<?php

declare(strict_types=1);

namespace Handlers\User;

use Models\User\UserRepository;
use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class UserProfileHandler extends AbstractHandler
{
    private const TOKEN_NAME = 'auth';

    /**
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        if ($authToken = $this->container->getCookies()->getCookie(self::TOKEN_NAME)) {
            $repository = new UserRepository($this->container);
            return $this->render('user/profile', ['user' => $repository->get($authToken)]);
        }

        return $this->render('user/profile');
    }
}
