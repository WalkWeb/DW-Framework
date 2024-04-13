<?php

namespace NW\Route;

use Countable;
use Iterator;
use NW\Traits\CollectionTrait;

// TODO Добавить объединение коллекций - таким образом можно будет делать коллекции под разные группы методов

class RouteCollection implements Iterator, Countable
{
    use CollectionTrait;

    /**
     * @var Route[]
     */
    private array $elements = [];

    public function get($name, $path, $handler, $param = [], $namespace = ''): Route
    {
        $route = new Route($name, $path, $handler, 'GET', $param, $namespace);
        $this->elements[] = $route;
        return $route;
    }

    public function post($name, $path, $handler, $param = [], $namespace = ''): Route
    {
        $route = new Route($name, $path, $handler, 'POST', $param, $namespace);
        $this->elements[] = $route;
        return $route;
    }

    // TODO put, delete

    public function addMiddleware(string $middleware, int $priority = Route::DEFAULT_PRIORITY): self
    {
        foreach ($this->elements as $route) {
            $route->addMiddleware($middleware, $priority);
        }

        return $this;
    }

    public function current(): Route
    {
        return current($this->elements);
    }
}
