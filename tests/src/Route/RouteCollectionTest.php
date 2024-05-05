<?php

declare(strict_types=1);

namespace Tests\src\Route;

use WalkWeb\NW\Route\Route;
use WalkWeb\NW\Route\RouteCollection;
use Tests\AbstractTest;

class RouteCollectionTest extends AbstractTest
{
    /**
     * Тест на создание маршрутов через методы в коллекции get(), post(), put(), delete()
     *
     * @dataProvider createDataProvider
     * @param string $method
     * @param string $expectedMethod
     * @param string $name
     * @param string $path
     * @param string $handler
     * @param array $param
     * @param string $namespace
     */
    public function testRouteCollectionCreate(
        string $method,
        string $expectedMethod,
        string $name,
        string $path,
        string $handler,
        array $param,
        string $namespace
    ): void
    {
        $routes = new RouteCollection();

        $routes->$method($name, $path, $handler, $param, $namespace);

        self::assertCount(1, $routes);

        foreach ($routes as $route) {
            self::assertEquals($expectedMethod, $route->getMethod());
            self::assertEquals($name, $route->getName());
            self::assertEquals($path, $route->getPath());
            self::assertEquals($namespace . '\\' . $handler, $route->getHandler());
            self::assertEquals($param, $route->getParams());
            self::assertEquals($namespace, $route->getNamespace());
        }

        self::assertNull($routes->key());
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

    /**
     * Данные отличаются только вызываемым методом и ожидаемым созданным http-методом
     *
     * @return array[]
     */
    public function createDataProvider(): array
    {
        return [
            [
                'get',                 // вызываемый метод в коллекции
                'GET',                 // ожидаемый http метод
                'testGetRoute',        // название роута
                'home',                // uri
                'TestContoller@index', // хандлер
                ['test' => 'test'],    // параметры
                'namespace',           // namespace
            ],
            [
                'post',
                'POST',
                'testGetRoute',
                'home',
                'TestContoller@index',
                ['test' => 'test'],
                'namespace',
            ],
            [
                'put',
                'PUT',
                'testGetRoute',
                'home',
                'TestContoller@index',
                ['test' => 'test'],
                'namespace',
            ],
            [
                'delete',
                'DELETE',
                'testGetRoute',
                'home',
                'TestContoller@index',
                ['test' => 'test'],
                'namespace',
            ],
        ];
    }
}
