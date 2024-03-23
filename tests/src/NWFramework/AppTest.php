<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\App;
use NW\AppException;
use NW\Request;
use NW\Response;
use NW\Route\RouteCollection;
use NW\Route\Router;
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
        $app = $this->getApp($this->createRouter('/', 'MainHandler'));

        $response = $app->handle($request);

        self::assertRegExp('/Главная страница/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    /**
     * @throws AppException
     */
    public function testAppPageNotFound(): void
    {
        $app = $this->getApp(new Router(new RouteCollection()));
        $request = new Request(['REQUEST_URI' => '/']);

        $response = $app->handle($request);

        $expectedContent = <<<EOT
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Ошибка 404: Страница не найдена</title>
    <meta name="Description" content="">
    <meta name="Keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<div class="content">
    <h1>Ошибка 404: Страница не найдена</h1>
</body>
</html>
EOT;

        self::assertEquals($expectedContent, $response->getBody());
        self::assertEquals(Response::NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @throws AppException
     */
    public function testAppControllerNotFound(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $app = $this->getApp($this->createRouter('/', 'UnknownHandler'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(sprintf(App::ERROR_MISS_HANDLER, HANDLERS_DIR . '\UnknownHandler'));
        $this->expectExceptionCode(Response::INTERNAL_SERVER_ERROR);
        $app->handle($request);
    }

    /**
     * @throws AppException
     */
    public function testAppEmitSuccess(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $app = $this->getApp($this->createRouter('/', 'MainHandler'));

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
        $this->expectExceptionMessage(App::ERROR_MISS_CONTAINER);
        $this->expectExceptionCode(Response::INTERNAL_SERVER_ERROR);

        $reflectionClass = new ReflectionClass(App::class);

        $reflectionProperty = $reflectionClass->getProperty('container');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($reflectionClass, null);

        App::emit(new Response());
    }

    /**
     * @throws AppException
     */
    public function testAppCreateInternalErrorResponseExistTemplateSuccess(): void
    {
        $router = new Router(new RouteCollection());
        new App($router, $this->getContainer());

        $response = App::createInternalErrorResponse();

        $content = <<<EOT
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>500: Internal Server Error</title>
    <meta name="Description" content="">
    <meta name="Keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<div class="content">
    <h1 class="center">500: Internal Server Error</h1>
    <p class="center">Кажется что-то сломалось. Попробуйте обратиться позже.</p>
</body>
</html>
EOT;

        self::assertEquals(Response::INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertEquals($content, $response->getBody());
    }

    /**
     * @throws AppException
     */
    public function testAppCreateInternalErrorResponseNoTemplateSuccess(): void
    {
        $router = new Router(new RouteCollection());
        new App($router, $this->getContainer(APP_ENV, 'unknown_view'));

        $response = App::createInternalErrorResponse();

        $content = '500: Internal Server Error';

        self::assertEquals(Response::INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertEquals($content, $response->getBody());
    }

    /**
     * @throws AppException
     * @throws ReflectionException
     */
    public function testAppCreateInternalErrorResponseFail(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage(App::ERROR_MISS_CONTAINER);
        $this->expectExceptionCode(Response::INTERNAL_SERVER_ERROR);

        $reflectionClass = new ReflectionClass(App::class);

        $reflectionProperty = $reflectionClass->getProperty('container');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($reflectionClass, null);

        App::createInternalErrorResponse();
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
