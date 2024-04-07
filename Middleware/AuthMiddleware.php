<?php

declare(strict_types=1);

namespace Middleware;

use Domain\User\UserInterface;
use Domain\User\UserRepository;
use NW\AbstractMiddleware;
use NW\AppException;
use NW\Request;
use NW\Response;

class AuthMiddleware extends AbstractMiddleware
{
    /**
     * @param Request $request
     * @param callable $handler
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request, callable $handler): Response
    {
        if ($authToken = $this->container->getCookies()->get(UserInterface::AUTH_TOKEN)) {
            $repository = new UserRepository($this->container);
            if ($user = $repository->get($authToken)) {
                $this->container->setTemplate($user->getTemplate());
                $this->container->set('user', $user);
            }
        }

        return $handler($request);
    }
}
