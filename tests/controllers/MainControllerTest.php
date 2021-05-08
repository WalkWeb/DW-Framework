<?php

namespace Tests\controllers;

use NW\Route\RouteCollection;
use NW\Route\Router;
use PHPUnit\Framework\TestCase;
use NW\Request\Request;
use NW\App\App;

class MainControllerTest extends TestCase
{
    /** @var App */
    private $app;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../../config.local.php';

        $routes = new RouteCollection();
        $routes->get('home', '/', 'MainController@index');
        $router = new Router($routes);

        $this->app = new App($router);
    }

    /**
     * Проверяем ответ от главной страницы
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
     */
    public function testBadPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/no_page']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Страница не найдена/', $response->getBody());
        self::assertEquals(404, $response->getStatusCode());
    }
}
