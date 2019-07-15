<?php

namespace Middleware;

use NW\Middleware\MiddlewareInterface;
use NW\Request\Request;
use Middleware\Exceptions\AuthException;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @throws AuthException
     */
    public function __invoke(Request $request): void
    {
        // На данный момент механика Middleware проста - они делают какую-то проверку, и если она не проходит -
        // бросают исключения. Полноценного Pipeline пока нет.

        // Т.к. регистрация/авторизация на уровне фреймворка пока нет - просто кидаем исключение
        // Собственно задача этого Middleware в том, чтобы показать функционал их привязки к роутингу.

        throw new AuthException('Вы неавторизованы', 403);
    }
}
