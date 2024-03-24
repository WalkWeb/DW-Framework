<?php

declare(strict_types=1);

namespace Models\Post;

interface PostInterface
{
    // TODO public function getId(): string;
    // TODO public function getSlug(): string;
    public function getTitle(): string;
    public function getText(): string;
}
