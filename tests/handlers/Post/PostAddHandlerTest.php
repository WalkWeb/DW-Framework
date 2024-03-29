<?php

declare(strict_types=1);

namespace Tests\handlers\Post;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class PostAddHandlerTest extends AbstractTestCase
{
    /**
     * Тест на отображение формы для добавления поста
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
}
