<?php

declare(strict_types=1);

namespace Tests\src\Route;

use WalkWeb\NW\Request;
use WalkWeb\NW\Route\Route;
use Tests\AbstractTest;

class RouteTest extends AbstractTest
{
    /**
     * Тест на создание маршрута
     */
    public function testRouteCreate(): void
    {
        $name = 'testGetRoute';
        $path = 'home';
        $handler = 'TestContoller@index';
        $method = 'GET';
        $params = ['test' => 'test'];
        $namespace = 'namespace';

        $route = new Route($name, $path, $handler, $method, $params, $namespace);

        self::assertEquals($name, $route->getName());
        self::assertEquals($path, $route->getPath());
        self::assertEquals($namespace . '\\' . $handler, $route->getHandler());
        self::assertEquals($method, $route->getMethod());
        self::assertEquals($params, $route->getParams());
        self::assertEquals($namespace, $route->getNamespace());
        self::assertEquals([], $route->getMiddleware());

        $route->addMiddleware('CreatedByMiddleware');

        self::assertEquals([Route::DEFAULT_PRIORITY => 'CreatedByMiddleware'], $route->getMiddleware());
    }

    /**
     * Тесты на совпадение маршрута
     *
     * @dataProvider matchDataProvider
     * @param Request $request
     * @param string $name
     * @param string $path
     * @param string $handler
     * @param string $method
     * @param array $params
     * @param string $namespace
     * @param array|null $expectedResult
     */
    public function testRouteMatch(
        Request $request,
        string $name,
        string $path,
        string $handler,
        string $method,
        array $params,
        string $namespace,
        ?array $expectedResult
    ): void
    {
        $route = new Route($name, $path, $handler, $method, $params, $namespace);
        self::assertEquals($expectedResult, $route->match($request));
    }

    /**
     * @return array
     */
    public function matchDataProvider(): array
    {
        $baseRequest = new Request([]);
        $postRequest = new Request(['REQUEST_URI' => '/post/10']);
        $postRequest->withAttribute('id', 10);
        $noMatchRequest = new Request(['REQUEST_URI' => '/post/abc']);

        return [
            // Совпадение маршрута и запроса
            [
                $baseRequest,
                'name',
                '/',
                'Handler',
                'GET',
                [],
                'namespace',
                [
                    'handler' => 'namespace\Handler',
                    'request' => $baseRequest,
                    'middleware' => [],
                ],
            ],
            // Не совпадение по методу
            [
                $baseRequest,
                'name',
                '/',
                'Handler',
                'POST',
                [],
                'namespace',
                null,
            ],
            // Совпадение маршрута и запроса + параметр
            [
                $postRequest,
                'name',
                '/post/{id}',
                'GetPostHandler',
                'GET',
                ['id' => '\d+'],
                'namespace',
                [
                    'handler' => 'namespace\GetPostHandler',
                    'request' => $postRequest,
                    'middleware' => [],
                ],
            ],
            // Не совпадение маршрута - ожидается int, а получен string в параметре
            [
                $noMatchRequest,
                'name',
                '/post/{id}',
                'GetPostHandler',
                'GET',
                ['id' => '\d+'],
                'namespace',
                null,
            ],
        ];
    }
}
