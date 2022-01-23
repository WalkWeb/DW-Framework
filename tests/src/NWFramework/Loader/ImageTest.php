<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Loader;

use NW\Loader\Image;
use Tests\AbstractTestCase;

class ImageTest extends AbstractTestCase
{
    public function testImageCreateDirNotNull(): void
    {
        $name = 'fileName';
        $type = 'png';
        $size = 1024;
        $width = 1000;
        $height = 500;
        $dir = 'directory';

        $file = new Image($name, $type, $size, $width, $height, $dir);

        self::assertEquals($name, $file->getName());
        self::assertEquals($type, $file->getType());
        self::assertEquals($size, $file->getSize());
        self::assertEquals($width, $file->getWidth());
        self::assertEquals($height, $file->getHeight());
        self::assertEquals($dir, $file->getDir());
    }

    public function testImageCreateNullDir(): void
    {
        $name = 'fileName';
        $type = 'png';
        $size = 1024;
        $width = 1000;
        $height = 500;

        $file = new Image($name, $type, $size, $width, $height);

        self::assertEquals($name, $file->getName());
        self::assertEquals($type, $file->getType());
        self::assertEquals($size, $file->getSize());
        self::assertEquals($width, $file->getWidth());
        self::assertEquals($height, $file->getHeight());
        self::assertNull($file->getDir());
    }
}
