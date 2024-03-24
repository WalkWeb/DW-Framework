<?php

declare(strict_types=1);

namespace Models\Post;

interface PostInterface
{
    public function getId(): string;
    public function getSlug(): string;
    public function getTitle(): string;
    public function getText(): string;
}
