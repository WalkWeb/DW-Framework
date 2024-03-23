<?php

declare(strict_types=1);

namespace Tests\handlers\Post;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class PostCreateHandlerTest extends AbstractTestCase
{
    // TODO testPostCreateSuccess - нужно доработать класс Captcha

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
