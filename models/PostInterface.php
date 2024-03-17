<?php

declare(strict_types=1);

namespace Models;

interface PostInterface
{
    // TODO public function getId(): string;
    public function getTitle(): string;
    public function getText(): string;
}
