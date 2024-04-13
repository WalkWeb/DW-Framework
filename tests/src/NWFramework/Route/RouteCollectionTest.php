<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Route;

use NW\Route\Route;
use NW\Route\RouteCollection;
use Tests\AbstractTest;

class RouteCollectionTest extends AbstractTest
{
    public function testRouteCollectionGet(): void
    {
        $routes = new RouteCollection();

        $name = 'testGetRoute';
        $path = 'home';
        $handler = 'TestContoller@index';
        $param = ['test' => 'test'];
        $namespace = 'namespace';

        $routes->get($name, $path, $handler, $param, $namespace);

        self::assertCount(1, $routes);

        foreach ($routes as $route) {
            self::assertEquals('GET', $route->getMethod());
            self::assertEquals($name, $route->getName());
            self::assertEquals($path, $route->getPath());
            self::assertEquals($namespace . '\\' . $handler, $route->getHandler());
            self::assertEquals($param, $route->getParams());
            self::assertEquals($namespace, $route->getNamespace());
        }

        self::assertNull($routes->key());
    }

    public function testRouteCollectionPost(): void
    {
        $routes = new RouteCollection();

        $name = 'testPostRoute';
        $path = '/post/create';
        $handler = 'TestContoller@create';
        $param = ['body' => 'body'];
        $namespace = 'namespace';

        $routes->post($name, $path, $handler, $param, $namespace);

        self::assertCount(1, $routes);

        foreach ($routes as $route) {
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

        foreach ($routes as $route) {
            self::assertEquals([], $route->getMiddleware());
        }

        $routes->addMiddleware($middleware);

        foreach ($routes as $route) {
            self::assertEquals([Route::DEFAULT_PRIORITY => $middleware], $route->getMiddleware());
        }
    }
}
