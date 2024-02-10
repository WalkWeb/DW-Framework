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
        $image = $this->getLoader()->loaderImage($this->getFileData());

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
    public function testLoaderImageFailMaxSize(): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_SIZE);
        $this->getLoader()->loaderImage($this->getFileData(), 1000);
    }

    /**
     * @throws AppException
     * @throws LoaderException
     */
    public function testLoaderImageFailMaxWidth(): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_WIDTH);
        $this->getLoader()->loaderImage($this->getFileData(), 100000, 10);
    }

    /**
     * @throws AppException
     * @throws LoaderException
     */
    public function testLoaderImageFailMaxHeight(): void
    {
        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_HEIGHT);
        $this->getLoader()->loaderImage($this->getFileData(), 100000, 1000, 10);
    }

    /**
     * @throws AppException
     * @throws LoaderException
     */
    public function testLoaderImageFailFileType(): void
    {
        $data = [
            'file' => [
                'name' => 'file.odt',
                'type' => 'application/vnd.oasis.opendocument.text',
                'tmp_name' => __DIR__ . '/files/file.odt',
                'error' => 0,
                'size' => 8168,
            ],
        ];

        $this->expectException(LoaderException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_TYPE);
        $this->getLoader()->loaderImage($data, 100000, 1000, 10);
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
                'name' => 'ImageName',
                'type' => 'image/png',
                'tmp_name' => __DIR__ . '/files/image.png',
                'error' => 0,
                'size' => 37308,
            ],
        ];
    }
}
