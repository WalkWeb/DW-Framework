<?php

declare(strict_types=1);

namespace Tests\Handler\Image;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class ImageLoadHandlerTest extends AbstractTest
{
    /**
     * Тест на загрузку одной картинки
     *
     * Это простой тест, на то, что при загрузке картинки нет ошибки. Механика загрузки картинки более детально
     * проверяется в тестах LoaderImageTest
     *
     * @throws AppException
     */
    public function testImageLoadHandlerSuccess(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/image', 'REQUEST_METHOD'  => 'POST'],
            [],
            [],
            [],
            [
                'file' => [
                    'name'     => 'ImageName',
                    'type'     => 'image/png',
                    'tmp_name' => __DIR__ . '/files/image.png',
                    'error'    => 0,
                    'size'     => 37308,
                ],
            ]
        );
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        // По слову upload в html проверяем, что появился url к картинке
        self::assertRegExp('/upload/', $response->getBody());
    }

    /**
     * Простой тест на получение ошибки при загрузке одной картинки. Более детально ошибки проверяются в LoaderImageTest
     *
     * @throws AppException
     */
    public function testImageLoadHandlerFail(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/image', 'REQUEST_METHOD'  => 'POST'],
            [],
            [],
            [],
            [
                'file' => [
                    'name'     => 'ImageName',
                    'type'     => 'image/png',
                    'tmp_name' => __DIR__ . '/xxx/image.png',
                    'error'    => 0,
                    'size'     => 37308,
                ],
            ]
        );
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Ошибка: Loaded file not found/', $response->getBody());
    }
}
