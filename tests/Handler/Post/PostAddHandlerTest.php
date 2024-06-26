<?php

declare(strict_types=1);

namespace Tests\Handler\Post;

use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use Tests\AbstractTest;

class PostAddHandlerTest extends AbstractTest
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
