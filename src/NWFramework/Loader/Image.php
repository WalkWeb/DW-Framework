<?php

namespace NW\Loader;

class Image extends File
{
    /** @var int - Ширина изображения */
    public $width;

    /** @var int - Высота изображения */
    public $height;

    public function __construct(string $name, string $type, int $size, string $dir = null, $width, $height)
    {
        parent::__construct($name, $type, $size, $dir);

        $this->width = $width;
        $this->height = $height;
    }
}
