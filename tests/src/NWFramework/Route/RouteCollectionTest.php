<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Route;

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
            self::assertEquals('GET', $route->getMethod());
            self::assertEquals($name, $route->getName());
            self::assertEquals($path, $route->getPath());
            self::assertEquals($namespace . '\\' . $handler, $route->getHandler());
            self::assertEquals($param, $route->getParams());
            self::assertEquals($namespace, $route->getNamespace());
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
            self::assertEquals('POST', $route->getMethod());
            self::assertEquals($name, $route->getName());
            self::assertEquals($path, $route->getPath());
            self::assertEquals($namespace . '\\' . $handler, $route->getHandler());
            self::assertEquals($param, $route->getParams());
            self::assertEquals($namespace, $route->getNamespace());
        }
    }

    public function testRouteCollectionAddMiddleware(): void
    {
        $middleware = 'CreatedByMiddleware';
        $routes = new RouteCollection();

        $routes->get('posts', '/posts/{page}', 'Post\\PostGetListHandler', ['page' => '\d+']);
        $routes->get('post.id', '/post/{id}', 'Post\\PostGetHandler', ['id' => '\d+']);

        foreach ($routes->getRoutes() as $route) {
            self::assertEquals([], $route->getMiddleware());
        }

        $routes->addMiddleware($middleware);

        foreach ($routes->getRoutes() as $route) {
            self::assertEquals([$middleware], $route->getMiddleware());
        }
    }
}
