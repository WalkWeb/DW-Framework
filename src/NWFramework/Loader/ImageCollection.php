<?php

declare(strict_types=1);

namespace NW\Loader;

use Countable;
use Iterator;

class ImageCollection implements Iterator, Countable
{
    /** @var Image[] */
    private array $elements;

    private int $totalSize = 0;

    public function add(Image $image): void
    {
        $this->elements[] = $image;
        $this->totalSize += $image->getSize();
    }

    public function key()
    {
        return key($this->elements);
    }

    public function next()
    {
        return next($this->elements);
    }

    public function rewind(): void
    {
        reset($this->elements);
    }

    public function valid(): bool
    {
        return key($this->elements) !== null;
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function current(): Image
    {
        return current($this->elements);
    }

    public function getTotalSize(): int
    {
        return $this->totalSize;
    }
}
