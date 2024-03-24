<?php

namespace NW\Route;

use NW\AppException;
use NW\Request;

class Router
{
    public const ERROR_MISS_ROUTE = 'Route not found';

    private RouteCollection $routes;

    /**
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * На основе запроса возвращает имя контроллера и метод, который нужно вызвать
     *
     * @param Request $request
     * @return array
     * @throws AppException
     */
    public function getHandler(Request $request): array
    {
        foreach ($this->routes as $route) {
            if ($handler = $this->checkHandler($route, $request)) {
                return $handler;
            }
        }

        throw new AppException(self::ERROR_MISS_ROUTE);
    }

    /**
     * Проверяет соответствие маршрута методу
     *
     * А вообще, основная задача этого метода проверить, что элемент в массиве является объектом Route, и, phpStorm
     * корректно подхватывает его методы.
     *
     * @param Route $route
     * @param Request $request
     * @return array|null
     */
    public function checkHandler(Route $route, Request $request): ?array
    {
        if ($result = $route->match($request)) {
            return $result;
        }

        return null;
    }
}
