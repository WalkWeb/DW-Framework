<?php

declare(strict_types=1);

namespace Tests\handlers;

use NW\App;
use NW\AppException;
use NW\Request;
use NW\Response;
use NW\Route\RouteCollection;
use NW\Route\Router;
use Tests\AbstractTestCase;

class RedirectHandlerTest extends AbstractTestCase
{
    protected App $app;

    /**
     * @throws AppException
     */
    public function setUp(): void
    {
        parent::setUp();

        $routes = new RouteCollection();
        $routes->get('redirect.example', '/redirect', 'RedirectHandler');
        $router = new Router($routes);
        $this->app = new App($router, $this->getContainer());
    }

    /**
     * Тест на редирект
     *
     * @throws AppException
     */
    public function testRedirectPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/redirect']);
        $response = $this->app->handle($request);

        self::assertEquals(['Location' => 'https://www.google.com/'], $response->getHeaders());
        self::assertEquals(Response::MOVED_PERMANENTLY, $response->getStatusCode());
    }
}
