<?php

declare(strict_types=1);

namespace Tests\src\Loader;

use Tests\AbstractTest;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Loader\Image;
use WalkWeb\NW\Loader\LoaderException;
use WalkWeb\NW\Loader\SimpleImageResizer;

class SimpleImageResizerTest extends AbstractTest
{
    /**
     * @dataProvider successDataProvider
     * @param Image $image
     * @throws AppException
     */
    public function testSimpleImageResizerSuccess(Image $image): void
    {
        $resizeImagePath = SimpleImageResizer::resize($image, 300, 300);

        $path = DIR . '/public/' . $resizeImagePath;

        self::assertFileExists($path);
    }

    /**
     * @throws AppException
     */
    public function testSimpleImageResizerNoResize(): void
    {
        $image = new Image(
            'test image',
            'png',
            39852,
            398,
            261,
            __DIR__ . '/files/01.png',
            'file_path'
        );

        self::assertEquals($image->getFilePath(), SimpleImageResizer::resize($image, 500, 300));
    }

    public function testSimpleImageResizerAbsolutePathNotFound(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_NO_DIRECTORY);
        SimpleImageResizer::resize($this->getImage(), 300, 300, 50, '/invalid_dir/');
    }

    /**
     * @return array
     */
    public function successDataProvider(): array
    {
        return [
            // jpeg
            [
                new Image(
                    'test image',
                    'jpg',
                    38673,
                    642,
                    666,
                    __DIR__ . '/files/03.jpg',
                    'file_path'
                )
            ],
            // png
            [
                new Image(
                    'test image',
                    'png',
                    138388,
                    642,
                    666,
                    __DIR__ . '/files/04.png',
                    'file_path'
                )
            ],
            // gif
            [
                new Image(
                    'test image',
                    'gif',
                    100700,
                    642,
                    666,
                    __DIR__ . '/files/05.gif',
                    'file_path'
                )
            ],
        ];
    }

    /**
     * @return Image
     */
    private function getImage(): Image
    {
        $name = 'test image';
        $type = 'jpg';
        $size = 38673;
        $width = 642;
        $height = 666;
        $absoluteDir = __DIR__ . '/files/';
        $absoluteFilePath = $absoluteDir . '03.jpg';
        $filePath = 'file_path';

        return new Image($name, $type, $size, $width, $height, $absoluteFilePath, $filePath);
    }
}
