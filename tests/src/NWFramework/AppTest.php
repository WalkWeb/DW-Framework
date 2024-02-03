<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\App;
use NW\AppException;
use NW\Request;
use NW\Response;
use NW\Route\RouteCollection;
use NW\Route\Router;
use NW\Utils\HttpCode;
use ReflectionClass;
use ReflectionException;
use Tests\AbstractTestCase;

class AppTest extends AbstractTestCase
{
    /**
     * @throws AppException
     */
    public function testAppPageFound(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $app = $this->getApp($this->createRouter('/', 'MainController@index'));

        $response = $app->handle($request);

        self::assertRegExp('/Главная страница/', $response->getBody());
        self::assertEquals(HttpCode::OK, $response->getStatusCode());
    }

    /**
     * @throws AppException
     */
    public function testAppPageNotFound(): void
    {
        $app = $this->getApp(new Router(new RouteCollection()));
        $request = new Request(['REQUEST_URI' => '/']);

        $response = $app->handle($request);

        self::assertEquals('404: Page not found', $response->getBody());
        self::assertEquals(HttpCode::NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @throws AppException
     */
    public function testAppControllerNotFound(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $app = $this->getApp($this->createRouter('/', 'UnknownController@index'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Отсутствует контроллер: Controllers\UnknownController');
        $this->expectExceptionCode(HttpCode::INTERNAL_SERVER_ERROR);
        $app->handle($request);
    }

    /**
     * @throws AppException
     */
    public function testAppMethodNotFound(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $app = $this->getApp($this->createRouter('/', 'MainController@unknownMethod'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Метод не найден: unknownMethod');
        $this->expectExceptionCode(HttpCode::INTERNAL_SERVER_ERROR);
        $app->handle($request);
    }

    /**
     * @throws AppException
     */
    public function testAppEmitSuccess(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $app = $this->getApp($this->createRouter('/', 'MainController@index'));

        $response = $app->handle($request);

        ob_start();
        App::emit($response);
        $content = ob_get_clean();

        // TODO Когда из html будет убрана статистика по используемой памяти/времени выполнении - сделать проверку на точное совпадение строк
        self::assertRegExp('/Главная страница нашего замечательного сайта./', $content);
    }

    /**
     * @throws AppException
     * @throws ReflectionException
     */
    public function testAppEmitError(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Метод emit не может вызываться до создания App');
        $this->expectExceptionCode(HttpCode::INTERNAL_SERVER_ERROR);

        $reflectionClass = new ReflectionClass(App::class);

        $reflectionProperty = $reflectionClass->getProperty('container');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($reflectionClass, null);

        App::emit(new Response());
    }

    /**
     * @param string $path
     * @param string $handler
     * @return Router
     */
    private function createRouter(string $path, string $handler): Router
    {
        $routes = new RouteCollection();
        $routes->get('test', $path, $handler);
        return new Router($routes);
    }
}
