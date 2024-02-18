<?php

namespace NW\Loader;

class File
{
    private string $name;
    private string $type;
    private string $dir;
    private int $size;

    public function __construct(string $name, string $type, int $size, string $dir)
    {
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->dir = $dir;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAbsoluteFilePath(): string
    {
        return $this->dir . $this->name . $this->type;
    }
}
