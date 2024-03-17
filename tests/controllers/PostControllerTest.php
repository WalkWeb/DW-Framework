<?php

declare(strict_types=1);

namespace Tests\controllers;

use NW\App;
use NW\AppException;
use NW\Request;
use NW\Response;
use NW\Route\RouteCollection;
use NW\Route\Router;
use Tests\AbstractTestCase;

class PostControllerTest extends AbstractTestCase
{
    private App $app;

    /**
     * @throws AppException
     */
    public function setUp(): void
    {
        parent::setUp();

        $routes = new RouteCollection();
        $routes->get('posts', '/posts/{page}', 'PostController@index', ['page' => '\d+']);
        $routes->get('post.id', '/post/{id}', 'PostController@view', ['id' => '\d+']);
        $routes->get('post.add', '/post/create', 'PostController@add');
        $routes->post('post.create', '/post/create', 'PostController@create');
        $router = new Router($routes);
        $this->app = new App($router, $this->getContainer());
    }

    /**
     * Тест на получения конкретного поста
     *
     * @throws AppException
     */
    public function testPostGetOne(): void
    {
        $request = new Request(['REQUEST_URI' => '/post/3']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Заголовок третьего поста/', $response->getBody());
        self::assertRegExp('/Содержимое третьего поста/', $response->getBody());
        self::assertRegExp('/Вернуться к списку постов/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    /**
     * Тест на получения списка постов
     *
     * @throws AppException
     */
    public function testPostGetList(): void
    {
        $request = new Request(['REQUEST_URI' => '/posts/1']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Посты/', $response->getBody());
        self::assertRegExp('/Заголовок первого поста/', $response->getBody());
        self::assertRegExp('/Содержимое первого поста/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    /**
     * Тест на получение формы для добавления поста
     *
     * @throws AppException
     */
    public function testPostAdd(): void
    {
        $request = new Request(['REQUEST_URI' => '/post/create']);
        $response = $this->app->handle($request);

        self::assertRegExp('/Добавить новый пост/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    // TODO testPostCreateSuccess

    /**
     * Тест на создание нового поста
     *
     * @throws AppException
     */
    public function testPostCreateFailCaptcha(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/post/create', 'REQUEST_METHOD' => 'POST'],
            ['title' => 'title', 'text' => 'text', 'captcha' => '123']
        );
        $response = $this->app->handle($request);

        self::assertRegExp('/Символы с картинки указаны неверно/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }
}
