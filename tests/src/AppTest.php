<?php

declare(strict_types=1);

namespace Tests\src;

use WalkWeb\NW\App;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use WalkWeb\NW\Route\RouteCollection;
use WalkWeb\NW\Route\Router;
use ReflectionClass;
use ReflectionException;
use Tests\AbstractTest;
use Handler\MainHandler;
use Tests\utils\ExampleHandler;
use Tests\utils\InvalidMiddleware;
use Tests\utils\Middleware1;
use Tests\utils\Middleware2;
use Tests\utils\Middleware3;

class AppTest extends AbstractTest
{
    /**
     * @throws AppException
     */
    public function testAppPageFound(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $app = $this->getApp($this->createRouter('/', MainHandler::class));

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
        $this->expectExceptionMessage(sprintf(App::ERROR_MISS_HANDLER, 'UnknownHandler'));
        $this->expectExceptionCode(Response::INTERNAL_SERVER_ERROR);
        $app->handle($request);
    }

    /**
     * @throws AppException
     */
    public function testAppEmitSuccess(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $app = $this->getApp($this->createRouter('/', MainHandler::class));

        $response = $app->handle($request);

        ob_start();
        App::emit($response);
        $content = ob_get_clean();

        $expectedContent = <<<EOT
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Главная страница</title>
    <meta name="Description" content="">
    <meta name="Keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<div class="menu">
    <ul class="navigation">
        <li><a href="/" title="">Главная</a></li>
        <li><a href="/posts/1" title="">Посты</a></li>
        <li><a href="/post/create" title="">Создать пост</a></li>
        <li><a href="/cookies" title="">Куки</a></li>
        <li><a href="/image" title="">Загрузка картинки</a></li>
        <li><a href="/login" title="">Вход</a></li>
        <li><a href="/registration" title="">Регистрация</a></li>
        <li><a href="/profile" title="">Профиль</a></li>
        <li><a href="/logout" title=""><img src="/images/logout.png" class="logout" alt="" /></a></li>
    </ul>
</div>
<div class="content">
    
<h1>Главная страница</h1>

<p>Главная страница нашего замечательного сайта.</p>
    <hr color="#444">
    <label>
        Дизайн:
        <select name="select" id="template">
            <option value="value2" selected>default</option>
            <option value="value3">light</option>
        </select>
    </label>
</div>
<script src="/js/main.js?v=1.00"></script>
</body>
</html>
EOT;

        self::assertEquals($expectedContent, $content);
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
     * Тест на ситуацию, когда указанного middleware не существует
     *
     * @throws AppException
     */
    public function testAppMissMiddleware(): void
    {
        $middleware = 'UnknownMiddleware';
        $routes = new RouteCollection();
        $routes->get('test', '/', MainHandler::class)->addMiddleware($middleware);
        $router = new Router($routes);
        $request = new Request(['REQUEST_URI' => '/']);
        $container = $this->getContainer();
        $app = new App($router, $container);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(sprintf(App::ERROR_MISS_MIDDLEWARE, $middleware));
        $app->handle($request);
    }

    /**
     * Тест на ситуацию, когда middleware не имеет метода __invoke()
     *
     * @throws AppException
     */
    public function testAppInvalidMiddleware(): void
    {
        $middleware = InvalidMiddleware::class;
        $routes = new RouteCollection();
        $routes->get('test', '/', MainHandler::class)->addMiddleware($middleware);
        $router = new Router($routes);
        $request = new Request(['REQUEST_URI' => '/']);
        $container = $this->getContainer(APP_ENV, VIEW_DIR);
        $app = new App($router, $container);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(sprintf(App::ERROR_INVALID_MIDDLEWARE, $middleware));
        $app->handle($request);
    }

    /**
     * Тест на проверку очередности выполнения middleware по-умолчанию
     *
     * Middleware1 выполняется первым
     * Middleware3 выполняется последним
     *
     * @throws AppException
     */
    public function testDefaultMiddlewareDefault(): void
    {
        $routes = new RouteCollection();
        $routes->get('test', '/', ExampleHandler::class)
            ->addMiddleware(Middleware1::class)
            ->addMiddleware(Middleware2::class)
            ->addMiddleware(Middleware3::class)
        ;
        $router = new Router($routes);

        $container = $this->getContainer(APP_ENV, VIEW_DIR);

        $app = new App($router, $container);

        $request = new Request(['REQUEST_URI' => '/']);

        $response = $app->handle($request);

        self::assertEquals('[middleware-1][middleware-2][middleware-3]example html content', $response->getBody());
    }

    /**
     * Тест на проверку очередности выполнения middleware с пользовательскими приоритетами
     *
     * Middleware1 выполняется первым
     * Middleware3 выполняется последним
     *
     * @throws AppException
     */
    public function testDefaultMiddlewareCustom(): void
    {
        $routes = new RouteCollection();
        $routes->get('test', '/', ExampleHandler::class)
            ->addMiddleware(Middleware1::class, 50)
            ->addMiddleware(Middleware2::class, 100)
            ->addMiddleware(Middleware3::class, 10)
        ;
        $router = new Router($routes);

        $container = $this->getContainer(APP_ENV, VIEW_DIR);

        $app = new App($router, $container);

        $request = new Request(['REQUEST_URI' => '/']);

        $response = $app->handle($request);

        self::assertEquals('[middleware-2][middleware-1][middleware-3]example html content', $response->getBody());
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
