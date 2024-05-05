<?php

declare(strict_types=1);

namespace Tests\src\Loader;

use WalkWeb\NW\Loader\Image;
use Tests\AbstractTest;

class ImageTest extends AbstractTest
{
    public function testImageCreate(): void
    {
        $name = 'fileName';
        $type = 'png';
        $size = 1024;
        $width = 1000;
        $height = 500;
        $absoluteFilePath = 'absoluteFilePath';
        $filePath = 'filePath';

        $file = new Image($name, $type, $size, $width, $height, $absoluteFilePath, $filePath);

        self::assertEquals($name, $file->getName());
        self::assertEquals($type, $file->getType());
        self::assertEquals($size, $file->getSize());
        self::assertEquals($width, $file->getWidth());
        self::assertEquals($height, $file->getHeight());
        self::assertEquals($absoluteFilePath, $file->getAbsoluteFilePath());
        self::assertEquals($filePath, $file->getFilePath());
    }
}
