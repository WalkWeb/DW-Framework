<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Loader;

use NW\AppException;
use NW\Loader\LoaderException;
use NW\Loader\LoaderImage;
use Tests\AbstractTestCase;

class LoaderImageTest extends AbstractTestCase
{
    /**
     * @throws LoaderException
     * @throws AppException
     */
    public function testLoaderImageSuccess(): void
    {
        $image = $this->getLoader()->load($this->getFileData());

        $fileDir = substr($this->dir, 0, -5) . 'public/images/upload/';
        $filePath = $fileDir . $image->getName() . $image->getType();

        self::assertFileExists($image->getFilePath());
        self::assertEquals('7965a59138ffd57ae30eb9cac9439a6a', $image->getName());
        self::assertEquals('.png', $image->getType());
        self::assertEquals(357, $image->getWidth());
        self::assertEquals(270, $image->getHeight());
        self::assertEquals(37308, $image->getSize());
        self::assertEquals($fileDir, $image->getDir());
        self::assertEquals($filePath, $image->getFilePath());
    }

    /**
     * @throws AppException
     * @throws LoaderException
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
        $this->expectExceptionMessage('Загружаемый файл был получен только частично');
        $this->getLoader()->load($data);
    }

    /**
     * @throws AppException
     * @throws LoaderException
     */
    public function testLoaderImageFailMaxSize(): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_SIZE);
        $this->getLoader()->load($this->getFileData(), 1000);
    }

    /**
     * @throws AppException
     * @throws LoaderException
     */
    public function testLoaderImageFailMaxWidth(): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_WIDTH);
        $this->getLoader()->load($this->getFileData(), 100000, 10);
    }

    /**
     * @throws AppException
     * @throws LoaderException
     */
    public function testLoaderImageFailMaxHeight(): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_HEIGHT);
        $this->getLoader()->load($this->getFileData(), 100000, 1000, 10);
    }

    /**
     * Тесты на различные варианты невалидных данных о файле
     *
     * @dataProvider invalidFileDataProvider
     * @param array $data
     * @param string $error
     * @throws AppException
     */
    public function testLoaderImageInvalidFileData(array $data, string $error): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage($error);
        $this->getLoader()->load($data);
    }

    /**
     * @throws AppException
     * @throws LoaderException
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
        $this->expectExceptionMessage(LoaderException::ERROR_TYPE);
        $this->getLoader()->load($data, 100000, 1000, 10);
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

    /**
     * @return LoaderImage
     * @throws AppException
     */
    private function getLoader(): LoaderImage
    {
        return new LoaderImage($this->getContainer());
    }

    private function getFileData(): array
    {
        return [
            'file' => [
                'name'     => 'ImageName',
                'type'     => 'image/png',
                'tmp_name' => __DIR__ . '/files/image.png',
                'error'    => 0,
                'size'     => 37308,
            ],
        ];
    }
}
