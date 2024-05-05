<?php

declare(strict_types=1);

namespace Tests\Handler\Image;

use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use Tests\AbstractTest;

class ImageIndexHandlerTest extends AbstractTest
{
    /**
     * Тест на отображение формы загрузки картинок
     *
     * @throws AppException
     */
    public function testImageIndexHandler(): void
    {
        $request = new Request(['REQUEST_URI' => '/image']);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Загрузка картинки/', $response->getBody());
    }
}
