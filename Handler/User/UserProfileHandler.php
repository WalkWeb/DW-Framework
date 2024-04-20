<?php

declare(strict_types=1);

namespace Handler\User;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class UserProfileHandler extends AbstractHandler
{
    /**
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        if ($this->container->exist('user')) {
            return $this->render('user/profile', ['user' => $this->container->getUser()]);
        }

        return $this->render('user/profile');
    }
}
