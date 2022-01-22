<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Route;

use Middleware\Exceptions\AuthException;
use NW\Request\Request;
use NW\Route\Route;
use NW\Utils\HttpCode;
use Tests\AbstractTestCase;

class RouteTest extends AbstractTestCase
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

        self::assertEquals($name, $route->name);
        self::assertEquals($path, $route->path);
        self::assertEquals($namespace . '\\' . $handler, $route->handler);
        self::assertEquals($method, $route->method);
        self::assertEquals($params, $route->params);
        self::assertEquals($namespace, $route->namespace);
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
     * Тест на выполнение middleware
     *
     * Полноценная механика Middleware пока не реализована, так что это пока только пародия нан их
     */
    public function testRouteRunMiddleware(): void
    {
        $name = 'testGetRoute';
        $path = 'home';
        $handler = 'TestContoller@index';
        $method = 'GET';
        $params = ['test' => 'test'];
        $namespace = '';

        $route = new Route($name, $path, $handler, $method, $params, $namespace);
        $route->addMiddleware('AuthMiddleware');

        // Так как мы не авторизованы - при выполнении middleware будет исключение
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage(AuthException::UNAUTHORIZED);
        $this->expectExceptionCode(HttpCode::UNAUTHORIZED);
        $route->runMiddleware(new Request([]));
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
                'Controller@method',
                'GET',
                [],
                'namespace',
                [
                    'handler' => 'namespace\Controller@method',
                    'request' => $baseRequest,
                ],
            ],
            // Не совпадение по методу
            [
                $baseRequest,
                'name',
                '/',
                'Controller@method',
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
                'Controller@getPost',
                'GET',
                ['id' => '\d+'],
                'namespace',
                [
                    'handler' => 'namespace\Controller@getPost',
                    'request' => $postRequest,
                ],
            ],
            // Не совпадение маршрута - ожидается int, а получен string в параметре
            [
                $noMatchRequest,
                'name',
                '/post/{id}',
                'Controller@getPost',
                'GET',
                ['id' => '\d+'],
                'namespace',
                null,
            ],
        ];
    }
}
