<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Route;

use Middleware\AuthMiddleware;
use NW\Route\Route;
use NW\Route\RouteCollection;
use Tests\AbstractTestCase;

class RouteCollectionTest extends AbstractTestCase
{
    public function testRouteCollectionGet(): void
    {
        $routeCollection = new RouteCollection();

        $name = 'testGetRoute';
        $path = 'home';
        $handler = 'TestContoller@index';
        $param = ['test' => 'test'];
        $namespace = 'namespace';

        $routeCollection->get($name, $path, $handler, $param, $namespace);

        self::assertCount(1, $routeCollection->getRoutes());

        foreach ($routeCollection->getRoutes() as $route) {
            self::assertEquals('GET', $route->method);
            self::assertEquals($name, $route->name);
            self::assertEquals($path, $route->path);
            self::assertEquals($namespace . '\\' . $handler, $route->handler);
            self::assertEquals($param, $route->params);
            self::assertEquals($namespace, $route->namespace);
        }
    }

    public function testRouteCollectionPost(): void
    {
        $routeCollection = new RouteCollection();

        $name = 'testPostRoute';
        $path = '/post/create';
        $handler = 'TestContoller@create';
        $param = ['body' => 'body'];
        $namespace = 'namespace';

        $routeCollection->post($name, $path, $handler, $param, $namespace);

        self::assertCount(1, $routeCollection->getRoutes());

        foreach ($routeCollection->getRoutes() as $route) {
            self::assertEquals('POST', $route->method);
            self::assertEquals($name, $route->name);
            self::assertEquals($path, $route->path);
            self::assertEquals($namespace . '\\' . $handler, $route->handler);
            self::assertEquals($param, $route->params);
            self::assertEquals($namespace, $route->namespace);
        }
    }

    public function testRouteCollectionMiddleware(): void
    {
        $routeCollection = new RouteCollection();

        $route = new Route('test', 'path', 'Controller@index', 'GET');

        $routeCollection->middleware($route, AuthMiddleware::class);

        self::assertEquals([AuthMiddleware::class], $route->middleware);
    }
}
