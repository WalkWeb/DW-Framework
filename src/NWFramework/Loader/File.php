<?php

namespace NW\Loader;

class File
{
    private $name;

    private $type;

    private $dir;

    private $size;

    public function __construct(string $name, string $type, int $size, string $dir = null)
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

    public function getDir(): ?string
    {
        return $this->dir;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
