<?php

namespace Tests\controllers;

use Exception;
use NW\AppException;
use NW\Response;
use NW\Route\RouteCollection;
use NW\Route\Router;
use NW\Request;
use NW\App;
use Tests\AbstractTestCase;

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
        $routes->get('home', '/', 'MainController@index');
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
    public function testBadPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/no_page']);
        $response = $this->app->handle($request);

        self::assertEquals('404: Page not found', $response->getBody());
        self::assertEquals(Response::NOT_FOUND, $response->getStatusCode());
    }
}
