<?php

namespace NW\Loader;

// TODO Убрать публичные свойства

class Image extends File
{
    /** @var int - Ширина изображения */
    public $width;

    /** @var int - Высота изображения */
    public $height;

    public function __construct(string $name, string $type, int $size, int $width, int $height, string $dir = null)
    {
        parent::__construct($name, $type, $size, $dir);

        $this->width = $width;
        $this->height = $height;
    }
}
