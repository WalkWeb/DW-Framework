<?php

namespace NW\Route;

class RouteCollection
{
    // TODO Добавить объединение коллекций - таким образом можно будет делать коллекции под разные группы методов
    // TODO Переделать на нормальную коллекцию, удалить getRoutes()

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

    public function addMiddleware(string $middleware): self
    {
        foreach ($this->routes as $route) {
            $route->addMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
