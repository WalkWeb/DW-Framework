<?php

namespace Tests\controllers;

use Exception;
use NW\Route\RouteCollection;
use NW\Route\Router;
use NW\Request\Request;
use NW\App\App;
use Tests\AbstractTestCase;

class MainControllerTest extends AbstractTestCase
{
    /** @var App */
    private $app;

    public function setUp(): void
    {
        parent::setUp();

        $routes = new RouteCollection();
        $routes->get('home', '/', 'MainController@index');
        $router = new Router($routes);

        $this->app = new App($router);
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
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * Проверяем ответ о несуществующей странице
     *
     * @throws Exception
     */
    public function testBadPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/no_page']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Страница не найдена/', $response->getBody());
        self::assertEquals(404, $response->getStatusCode());
    }
}
