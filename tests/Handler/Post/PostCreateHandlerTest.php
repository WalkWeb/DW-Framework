<?php

declare(strict_types=1);

namespace Tests\Handler\Post;

use Handler\Post\PostCreateHandler;
use WalkWeb\NW\App;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Container;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use WalkWeb\NW\Route\RouteCollection;
use WalkWeb\NW\Route\Router;
use Tests\AbstractTest;

class PostCreateHandlerTest extends AbstractTest
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
            ['title' => 'Заголовок', 'text' => 'text text text', 'captcha' => '1234', 'cstf' => '12345']
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
        $routes->post('post.create', '/post/create', PostCreateHandler::class);
        $app = new App(new Router($routes), $container);

        $request = new Request(
            ['REQUEST_URI' => '/post/create', 'REQUEST_METHOD' => 'POST'],
            ['title' => 'title', 'text' => 'text', 'captcha' => '123']
        );
        $response = $app->handle($request);

        self::assertRegExp('/Символы с картинки указаны неверно/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }

    /**
     * Тест на ситуацию, когда переданы невалидные данные
     *
     * @throws AppException
     */
    public function testPostCreateInvalidData(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/post/create', 'REQUEST_METHOD' => 'POST'],
            ['title' => 'H', 'text' => 'text text text', 'captcha' => '1234']
        );
        $response = $this->app->handle($request);

        self::assertRegExp('/Incorrect parameter &quot;title&quot;, should be min-max length: 2-50/', $response->getBody());
        self::assertEquals(Response::OK, $response->getStatusCode());
    }
}
