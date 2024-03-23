<?php

namespace Tests\handlers;

use Exception;
use NW\AppException;
use NW\Response;
use NW\Route\RouteCollection;
use NW\Route\Router;
use NW\Request;
use NW\App;
use Tests\AbstractTestCase;

// TODO Переписать в соответствии с заменой контроллеров на хандлеры

class MainControllerTest extends AbstractTestCase
{
    private App $app;

    /**
     * @throws AppException
     */
    public function setUp(): void
    {
        parent::setUp();

        $routes = new RouteCollection();
        $routes->get('home', '/', 'MainHandler');
        $router = new Router($routes);
        $this->app = new App($router, $this->getContainer());
    }

    /**
     * Проверяем ответ от главной страницы
     *
     * @throws Exception
     */
    public function testMainPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Главная страница/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    /**
     * Проверяем ответ о несуществующей странице
     *
     * @throws Exception
     */
    public function testNotFoundPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/no_page']);
        $response = $this->app->handle($request);

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
}
