<?php

namespace Models;

use NW\AppException;
use NW\Container;
use Models\Exceptions\PostException;

/**
 * Задача этого класса - продемонстрировать функционал валидации входящих данных
 *
 * Как таковой моделью этот класс, разумеется, не является.
 */
class Post
{
    private string $title;

    private string $text;

    private Container $container;

    /**
     * @param Container $container
     * @param string $title
     * @param string $text
     * @throws AppException
     */
    public function __construct(Container $container, string $title, string $text)
    {
        $this->container = $container;

        if (!$this->titleValidate($title)) {
            throw new PostException($container, $this->container->getValidator()->getError());
        }

        if (!$this->textValidate($text)) {
            throw new PostException($container, $this->container->getValidator()->getError());
        }

        $this->title = $title;
        $this->text = $text;
    }

    public function getTitle(): string
    {
        return htmlspecialchars($this->title);
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
