<?php

declare(strict_types=1);

namespace Handler\User;

use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

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
