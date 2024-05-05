<?php

namespace WalkWeb\NW\Loader;

class File
{
    private string $name;
    private string $type;
    private int $size;
    private string $absoluteFilePath;
    private string $filePath;

    public function __construct(
        string $name,
        string $type,
        int $size,
        string $absoluteFilePath,
        string $filePath
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->absoluteFilePath = $absoluteFilePath;
        $this->filePath = $filePath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getAbsoluteFilePath(): string
    {
        return $this->absoluteFilePath;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
