<?php

declare(strict_types=1);

namespace Tests\handlers\Post;

use NW\App;
use NW\AppException;
use NW\Container;
use NW\Request;
use NW\Response;
use NW\Route\RouteCollection;
use NW\Route\Router;
use Tests\AbstractTestCase;

class PostCreateHandlerTest extends AbstractTestCase
{
    /**
     * Тест на создание нового поста
     *
     * @throws AppException
     */
    public function testPostCreateSuccessCaptcha(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/post/create', 'REQUEST_METHOD' => 'POST'],
            ['title' => 'Заголовок', 'text' => 'text text text', 'captcha' => '1234']
        );
        $response = $this->app->handle($request);

        self::assertRegExp('/Сервер получил POST-данные/', $response->getBody());
        self::assertRegExp('/ID/', $response->getBody());
        self::assertRegExp('/Заголовок: Заголовок/', $response->getBody());
        self::assertRegExp('/Slug: zagolovok-/', $response->getBody());
        self::assertRegExp('/Содержимое: text text text/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    /**
     * Тест на создание нового поста
     *
     * @throws AppException
     */
    public function testPostCreateFailCaptcha(): void
    {
        $container = $this->getContainer(Container::APP_DEV);
        $routes = new RouteCollection();
        $routes->post('post.create', '/post/create', 'Post\\PostCreateHandler');
        $app = new App(new Router($routes), $container);

        $request = new Request(
            ['REQUEST_URI' => '/post/create', 'REQUEST_METHOD' => 'POST'],
            ['title' => 'title', 'text' => 'text', 'captcha' => '123']
        );
        $response = $app->handle($request);

        self::assertRegExp('/Символы с картинки указаны неверно/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }
}
