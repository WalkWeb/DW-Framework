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
     * @throws AppException
     */
    public function testSimpleImageResizerSuccess(): void
    {
        $resizeImagePath = SimpleImageResizer::resize($this->getImage(), 300, 300);

        $path = DIR . '/public/' . $resizeImagePath;

        self::assertFileExists($path);
    }

    public function testSimpleImageResizerAbsolutePathNotFound(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage(LoaderException::ERROR_NO_DIRECTORY);
        SimpleImageResizer::resize($this->getImage(), 300, 300, 50, '/invalid_dir/');
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
