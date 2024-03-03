<?php

namespace NW\Loader;

class Image extends File
{
    /**
     * Ширина изображения
     *
     * @var int
     */
    private int $width;

    /**
     * Высота изображения
     *
     * @var int
     */
    private int $height;

    public function __construct(
        string $name,
        string $type,
        int $size,
        int $width,
        int $height,
        string $absoluteFilePath,
        string $filePath
    ) {
        parent::__construct($name, $type, $size, $absoluteFilePath, $filePath);

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
