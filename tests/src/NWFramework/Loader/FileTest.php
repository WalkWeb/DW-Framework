<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Loader;

use NW\Loader\File;
use Tests\AbstractTest;

class FileTest extends AbstractTest
{
    public function testFileCreate(): void
    {
        $name = 'fileName';
        $type = 'png';
        $size = 1024;
        $absoluteFilePath = 'absoluteFilePath';
        $filePath = 'filePath';

        $file = new File($name, $type, $size, $absoluteFilePath, $filePath);

        self::assertEquals($name, $file->getName());
        self::assertEquals($type, $file->getType());
        self::assertEquals($size, $file->getSize());
        self::assertEquals($absoluteFilePath, $file->getAbsoluteFilePath());
        self::assertEquals($filePath, $file->getFilePath());
    }
}
