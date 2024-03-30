<?php

declare(strict_types=1);

namespace Models\Post;

interface PostInterface
{
    public const TITLE_MIN_LENGTH = 2;
    public const TITLE_MAX_LENGTH = 50;
    public const SLUG_MIN_LENGTH = 5;
    public const SLUG_MAX_LENGTH = 60;
    public const TEXT_MIN_LENGTH  = 3;
    public const TEXT_MAX_LENGTH  = 3000;

    public function getId(): string;
    public function getSlug(): string;
    public function getTitle(): string;
    public function getText(): string;
}
