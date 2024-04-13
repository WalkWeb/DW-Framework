<?php

declare(strict_types=1);

namespace Tests\utils;

use NW\AbstractMiddleware;
use NW\Request;
use NW\Response;

class Middleware3 extends AbstractMiddleware
{
    /**
     * Тестовый Middleware для проверки очередности выполнения
     *
     * @param Request $request
     * @param callable $handler
     * @return Response
     */
    public function __invoke(Request $request, callable $handler): Response
    {
        $response = $handler($request);
        $response->setBody('[middleware-3]' . $response->getBody());
        return $response;
    }
}
