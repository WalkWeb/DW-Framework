<?php

namespace NW\Route;

class RouteCollection
{
    // TODO Добавить добавление middleware сразу всей коллекции
    // TODO Добавить объединение коллекций - таким образом можно будет делать коллекции под разные группы методов

    /**
     * @var Route[]
     */
    private array $routes = [];

    public function get($name, $path, $handler, $param = [], $namespace = ''): Route
    {
        $route = new Route($name, $path, $handler, 'GET', $param, $namespace);
        $this->routes[] = $route;
        return $route;
    }

    public function post($name, $path, $handler, $param = [], $namespace = ''): Route
    {
        $route = new Route($name, $path, $handler, 'POST', $param, $namespace);
        $this->routes[] = $route;
        return $route;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
