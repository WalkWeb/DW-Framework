<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Loader;

use DateTime;
use Exception;
use NW\AppException;
use NW\Loader\Image;
use NW\Loader\LoaderException;
use NW\Loader\LoaderImage;
use RuntimeException;
use Tests\AbstractTest;

class LoaderImageTest extends AbstractTest
{
    /**
     * @dataProvider fileDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testLoaderImageSuccess(array $data): void
    {
        $image = $this->getLoader()->load($data);
        $filePath = $this->getFilePath($image);

        self::assertFileExists($image->getAbsoluteFilePath());
        self::assertEquals(10, strlen($image->getName()));
        self::assertEquals('.png', $image->getType());
        self::assertEquals(357, $image->getWidth());
        self::assertEquals(270, $image->getHeight());
        self::assertEquals(37308, $image->getSize());
        self::assertEquals($filePath, $image->getFilePath());
        self::assertEquals($this->getAbsoluteFilePath($filePath), $image->getAbsoluteFilePath());
    }

    /**
     * @dataProvider successFilesDataProvider
     * @param array $data
     * @param array $expectedSize
     * @throws Exception
     */
    public function testLoaderMultipleImageSuccess(array $data, array $expectedSize): void
    {
        $images = $this->getLoader()->multipleLoad($data);

        self::assertCount(2, $images);
        self::assertEquals(2, $images->count());
        self::assertEquals(array_sum($data['file']['size']), $images->getTotalSize());

        foreach ($images as $i => $image) {
            $filePath = $this->getFilePath($image);

            self::assertFileExists($image->getAbsoluteFilePath());
            self::assertEquals(10, strlen($image->getName()));
            self::assertEquals('.png', $image->getType());
            self::assertEquals($expectedSize[$i]['width'], $image->getWidth());
            self::assertEquals($expectedSize[$i]['height'], $image->getHeight());
            self::assertEquals($data['file']['size'][$i], $image->getSize());
            self::assertEquals($filePath, $image->getFilePath());
            self::assertEquals($this->getAbsoluteFilePath($filePath), $image->getAbsoluteFilePath());
        }
    }

    /**
     * Тест на различные варианты невалидных данных из $request->getFiles()
     *
     * @dataProvider failFilesDataProvider
     * @param array $data
     * @param string $expectedError
     * @throws Exception
     */
    public function testLoaderMultipleImageFail(array $data, string $expectedError): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage($expectedError);
        $this->getLoader()->multipleLoad($data);
    }

    /**
     * Тест на ситуацию, когда превышен лимит разово загружаемых картинок
     *
     * @dataProvider successFilesDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testLoaderImagesFailMaxLimit(array $data): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::LIMIT_IMAGES);
        $this->getLoader()->multipleLoad(
            $data,
            1000000,
            2000,
            2000,
            '/public/images/upload/',
            ['jpg', 'jpeg', 'gif', 'png'],
            1
        );
    }

    /**
     * @throws AppException
     * @throws Exception
     */
    public function testLoaderImageFileNotFound(): void
    {
        $data = [
            'file' => [
                'name'     => 'file_not_fund.png',
                'type'     => 'image/png',
                'tmp_name' => __DIR__ . '/files/file_not_fund.png',
                'error'    => 0,
                'size'     => 15000,
            ],
        ];

        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::FILE_NOT_FOUND);
        $this->getLoader()->load($data);
    }

    /**
     * @throws AppException
     * @throws Exception
     */
    public function testLoaderImageErrorCodeNoOk(): void
    {
        $data = [
            'file' => [
                'name'     => 'file.odt',
                'type'     => 'application/vnd.oasis.opendocument.text',
                'tmp_name' => __DIR__ . '/files/file.odt',
                'error'    => UPLOAD_ERR_PARTIAL,
                'size'     => 8168,
            ],
        ];

        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_PARTIAL);
        $this->getLoader()->load($data);
    }

    /**
     * @dataProvider fileDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testLoaderImageFailMaxSize(array $data): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::MAX_SIZE);
        $this->getLoader()->load($data, 1000);
    }

    /**
     * @dataProvider fileDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testLoaderImageFailMaxWidth(array $data): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::MAX_WIDTH);
        $this->getLoader()->load($data, 100000, 10);
    }

    /**
     * @dataProvider fileDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testLoaderImageFailMaxHeight(array $data): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::MAX_HEIGHT);
        $this->getLoader()->load($data, 100000, 1000, 10);
    }

    /**
     * Тесты на различные варианты невалидных данных о файле
     *
     * @dataProvider invalidFileDataProvider
     * @param array $data
     * @param string $error
     * @throws Exception
     */
    public function testLoaderImageInvalidFileData(array $data, string $error): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage($error);
        $this->getLoader()->load($data);
    }

    /**
     * @throws AppException
     * @throws Exception
     */
    public function testLoaderImageFailFileType(): void
    {
        $data = [
            'file' => [
                'name'     => 'file.odt',
                'type'     => 'application/vnd.oasis.opendocument.text',
                'tmp_name' => __DIR__ . '/files/file.odt',
                'error'    => 0,
                'size'     => 8168,
            ],
        ];

        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::INVALID_TYPE);
        $this->getLoader()->load($data, 100000, 1000, 10, '/public/images/upload/', ['gif']);
    }

    /**
     * @throws AppException
     * @throws Exception
     */
    public function testLoaderImageFailFileExtension(): void
    {
        $data = [
            'file' => [
                'name'     => 'ImageName',
                'type'     => 'image/png',
                'tmp_name' => __DIR__ . '/files/image.png',
                'error'    => 0,
                'size'     => 37308,
            ],
        ];

        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::INVALID_EXTENSION);
        $this->getLoader()->load($data, 100000, 1000, 10, '/public/images/upload/', ['gif']);
    }

    /**
     * @dataProvider fileDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testLoaderImageFailCreateDirectory(array $data): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('mkdir(): Permission denied');
        $this->getLoader()->load($data, 1000000, 2000, 2000, '/../../xxx/images/upload/');
    }

    public function invalidFileDataProvider(): array
    {
        return [
            [
                // Отсутствует file
                [],
                LoaderException::INVALID_FILE_DATA,
            ],
            [
                // file некорректного типа
                [
                    'file' => 'xxx',
                ],
                LoaderException::INVALID_FILE_DATA,
            ],
            [
                // Отсутствует tmp_name
                [
                    'file' => [
                        'error' => 0,
                    ],
                ],
                LoaderException::INVALID_TMP_NAME,
            ],
            [
                // tmp_name некорректного типа
                [
                    'file' => [
                        'tmp_name' => true,
                        'error'    => 0,
                    ],
                ],
                LoaderException::INVALID_TMP_NAME,
            ],
            [
                // Отсутствует error
                [
                    'file' => [
                        'tmp_name' => __DIR__ . '/files/file.odt',
                    ],
                ],
                LoaderException::INVALID_TMP_ERROR,
            ],
            [
                // error некорректного типа
                [
                    'file' => [
                        'tmp_name' => __DIR__ . '/files/file.odt',
                        'error'    => '0',
                    ],
                ],
                LoaderException::INVALID_TMP_ERROR,
            ],
        ];
    }

    public function fileDataProvider(): array
    {
        return [
            [
                [
                    'file' => [
                        'name'     => 'ImageName',
                        'type'     => 'image/png',
                        'tmp_name' => __DIR__ . '/files/image.png',
                        'error'    => 0,
                        'size'     => 37308,
                    ],
                ],
            ],
        ];
    }

    public function successFilesDataProvider(): array
    {
        return [
            [
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
                ],
                [
                    [
                        'width'  => 398,
                        'height' => 261,
                    ],
                    [
                        'width'  => 415,
                        'height' => 353,
                    ],
                ],
            ],
        ];
    }

    public function failFilesDataProvider(): array
    {
        return [
            [
                // Отсутствует file
                [],
                LoaderException::INVALID_FILE_DATA,
            ],
            [
                // file некорректного типа
                [
                    'file' => false,
                ],
                LoaderException::INVALID_FILE_DATA,
            ],
            [
                // Отсутствует ['file']['tmp_name']
                [
                    'file' => [
                        'error' => [],
                    ],
                ],
                LoaderException::INVALID_MULTIPLE_TMP_NAME,
            ],
            [
                // ['file']['tmp_name'] некорректного типа
                [
                    'file' => [
                        'tmp_name' => 'name',
                        'error'    => [],
                    ],
                ],
                LoaderException::INVALID_MULTIPLE_TMP_NAME,
            ],
            [
                // ['file']['tmp_name'] содержит не строки
                [
                    'file' => [
                        'tmp_name' => [1, 2, 3],
                        'error'    => [],
                    ],
                ],
                LoaderException::INVALID_MULTIPLE_TMP_NAME,
            ],
            [
                // Отсутствует ['file']['error']
                [
                    'file' => [
                        'tmp_name' => [],
                    ],
                ],
                LoaderException::INVALID_MULTIPLE_TMP_ERROR,
            ],
            [
                // ['file']['error'] некорректного типа
                [
                    'file' => [
                        'tmp_name' => [],
                        'error'    => 0,
                    ],
                ],
                LoaderException::INVALID_MULTIPLE_TMP_ERROR,
            ],
            [
                // ['file']['error'] содержит не int
                [
                    'file' => [
                        'tmp_name' => [],
                        'error'    => ['1', true],
                    ],
                ],
                LoaderException::INVALID_MULTIPLE_TMP_ERROR,
            ],
        ];
    }

    /**
     * @return LoaderImage
     * @throws AppException
     */
    private function getLoader(): LoaderImage
    {
        return new LoaderImage($this->getContainer());
    }

    /**
     * @param Image $image
     * @return string
     */
    private function getFilePath(Image $image): string
    {
        $date = new DateTime();

        return LoaderImage::FRONT_DIRECTORY .
            $date->format('Y') . '/' . $date->format('m') . '/' . $date->format('d') . '/' .
            $image->getName() . $image->getType();
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function getAbsoluteFilePath(string $filePath): string
    {
        return substr($this->dir, 0, -5) . 'public' . $filePath;
    }
}
