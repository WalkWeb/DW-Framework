<?php

declare(strict_types=1);

namespace Models\Post;

/**
 * Это новый вариант модели, в которой валидация вынесена во внешнюю фабрику
 */
class Post implements PostInterface
{
    private string $id;
    private string $title;
    private string $slug;
    private string $text;

    public function __construct(string $id, string $title, string $slug, string $text)
    {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
