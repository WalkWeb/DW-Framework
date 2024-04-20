<?php

declare(strict_types=1);

namespace Tests\Handler\Image;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTest;

class ImageMultipleLoadHandlerTest extends AbstractTest
{
    /**
     * Тест на загрузку нескольких картинок
     *
     * Это простой тест, на то, что при загрузке картинки нет ошибки. Механика загрузки картинки более детально
     * проверяется в тестах LoaderImageTest
     *
     * @throws AppException
     */
    public function testImageMultipleLoadHandlerSuccess(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/image_multiple', 'REQUEST_METHOD'  => 'POST'],
            [],
            [],
            [],
            [
                'file' => [
                    'name'     => [
                        '01.png',
                        '02.png',
                    ],
                    'type'     => [
                        'image/png',
                        'image/png',
                    ],
                    'tmp_name' => [
                        __DIR__ . '/files/01.png',
                        __DIR__ . '/files/02.png',
                    ],
                    'error'    => [
                        0,
                        0,
                    ],
                    'size'     => [
                        39852,
                        56418,
                    ],
                ],
            ]
        );
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        // По слову upload в html проверяем, что появился url к картинке
        self::assertRegExp('/upload/', $response->getBody());
    }

    /**
     * Простой тест на получение ошибки при загрузке нескольких картинок. Более детально ошибки проверяются в LoaderImageTest
     *
     * @throws AppException
     */
    public function testImageLoadHandlerFail(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/image_multiple', 'REQUEST_METHOD'  => 'POST'],
            [],
            [],
            [],
            [
                'file' => [
                    'name'     => [
                        '01.png',
                        '02.png',
                    ],
                    'type'     => [
                        'image/png',
                        'image/png',
                    ],
                    'tmp_name' => [
                        __DIR__ . '/xxx/01.png',
                        __DIR__ . '/yyy/02.png',
                    ],
                    'error'    => [
                        0,
                        0,
                    ],
                    'size'     => [
                        39852,
                        56418,
                    ],
                ],
            ]
        );
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertRegExp('/Ошибка: Loaded file not found/', $response->getBody());
    }
}
