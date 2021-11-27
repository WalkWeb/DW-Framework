<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Loader;

use NW\Loader\File;
use Tests\AbstractTestCase;

class FileTest extends AbstractTestCase
{
    public function testFileCreateDirNotNull(): void
    {
        $name = 'fileName';
        $type = 'png';
        $size = 1024;
        $dir = 'directory';

        $file = new File($name, $type, $size, $dir);

        self::assertEquals($name, $file->getName());
        self::assertEquals($type, $file->getType());
        self::assertEquals($size, $file->getSize());
        self::assertEquals($dir, $file->getDir());
    }

    public function testFileCreateNullDir(): void
    {
        $name = 'fileName';
        $type = 'png';
        $size = 1024;

        $file = new File($name, $type, $size);

        self::assertEquals($name, $file->getName());
        self::assertEquals($type, $file->getType());
        self::assertEquals($size, $file->getSize());
        self::assertNull($file->getDir());
    }
}
