<?php

namespace NW\Route;

class RouteCollection
{
    private $routes = [];

    public function get($name, $path, $handler, $param = [], $namespace = null): Route
    {
        $route = new Route($name, $path, $handler, 'GET', $param, $namespace);
        $this->routes[] = $route;
        return $route;
    }

    public function post($name, $path, $handler, $param = [], $namespace = null): Route
    {
        $route = new Route($name, $path, $handler, 'POST', $param, $namespace);
        $this->routes[] = $route;
        return $route;
    }

    public function middleware(Route $route, string $middleware): Route
    {
        $route->middleware($middleware);
        return $route;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
