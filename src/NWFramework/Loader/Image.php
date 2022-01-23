<?php

namespace NW\Loader;

class Image extends File
{
    /**
     * Ширина изображения
     *
     * @var int
     */
    private $width;

    /**
     * Высота изображения
     *
     * @var int
     */
    private $height;

    public function __construct(string $name, string $type, int $size, int $width, int $height, string $dir = null)
    {
        parent::__construct($name, $type, $size, $dir);

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }
}
