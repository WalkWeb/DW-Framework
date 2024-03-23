<?php

declare(strict_types=1);

namespace Tests\handlers\Post;

use NW\App;
use NW\AppException;
use NW\Route\RouteCollection;
use NW\Route\Router;
use Tests\AbstractTestCase;

abstract class AbstractPostHandlerTest extends AbstractTestCase
{
    // TODO Возможно стоит просто вынести в AbstractTestCase и добавить все существующие роуты

    protected App $app;

    /**
     * @throws AppException
     */
    public function setUp(): void
    {
        parent::setUp();

        $routes = new RouteCollection();
        $routes->get('posts', '/posts/{page}', 'Post\\PostGetListHandler', ['page' => '\d+']);
        $routes->get('post.id', '/post/{id}', 'Post\\PostGetHandler', ['id' => '\d+']);
        $routes->get('post.add', '/post/create', 'Post\\PostAddHandler');
        $routes->post('post.create', '/post/create', 'Post\\PostCreateHandler');
        $router = new Router($routes);
        $this->app = new App($router, $this->getContainer());
    }
}
