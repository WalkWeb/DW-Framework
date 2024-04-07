<?php

namespace Domain\Post;

use Exception;
use NW\AppException;
use NW\Container;
use NW\Traits\StringTrait;

/**
 * Это старый вариация модели в которой валидация интегрирована внутрь самой модели и реализована через класс Validator
 */
class LegacyPost implements PostInterface
{
    use StringTrait;

    private string $id;
    private string $title;
    private string $slug;
    private string $text;

    private Container $container;

    /**
     * @param Container $container
     * @param string $id
     * @param string $title
     * @param string $text
     * @throws AppException
     * @throws PostException
     */
    public function __construct(Container $container, string $id, string $title, string $text)
    {
        $this->container = $container;

        if (!$this->titleValidate($title)) {
            throw new PostException($this->container->getValidator()->getError());
        }

        if (!$this->textValidate($text)) {
            throw new PostException($this->container->getValidator()->getError());
        }

        try {
            $slugSuffix = '-' . random_int(10000, 99999);
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }

        $this->id = $id;
        $this->title = $title;
        $this->slug = strtolower(self::transliterate($title)) . $slugSuffix;
        $this->text = $text;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return htmlspecialchars($this->title);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getText(): string
    {
        return htmlspecialchars($this->text);
    }

    /**
     * @param string $title
     * @return bool
     * @throws AppException
     */
    private function titleValidate(string $title): bool
    {
        if (!$this->container->getValidator()->check('Заголовок', $title, [
            'required',
            'string',
            'min'    => 5,
            'max'    => 50,
            'parent' => '/^[a-zA-Z0-9а-яА-ЯёЁ\-_]*$/u',
        ])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $text
     * @return bool
     * @throws AppException
     */
    private function textValidate(string $text): bool
    {
        if (!$this->container->getValidator()->check('Содержимое поста', $text, [
            'required',
            'string',
            'min' => 5,
            'max' => 500,
        ])) {
            return false;
        }

        return true;
    }
}
