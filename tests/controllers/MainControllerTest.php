<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use NW\Request\Request;
use NW\App\App;
use NW\Response\Response;

class MainControllerTest extends TestCase
{
    /** @var App */
    private $app;

    protected function setUp()
    {
        require_once __DIR__ . '/../../config.local.php';
        $this->app = new App();
    }

    /**
     * Проверяем ответ от главной страницы
     */
    public function testMainPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/']);
        $response = $this->app->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertRegExp('/Главная страница/', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Проверяем ответ о несуществующей странице
     */
    public function testBadPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/no_page']);
        $response = $this->app->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertRegExp('/Страница не найдена/', $response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }
}
