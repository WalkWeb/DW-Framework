<?php

namespace Models;

use NW\Validator;
use Models\Exceptions\PostException;

/**
 * Задача этого класса - продемонстрировать функционал валидации входящих данных
 *
 * Как таковой моделью этот класс, разумеется, не является.
 */
class Post
{
    private $title;

    private $text;

    /**
     * @param string $title
     * @param string $text
     * @throws PostException
     */
    public function __construct(string $title, string $text)
    {
        if (!$this->titleValidate($title)) {
            throw new PostException(Validator::getError());
        }

        if (!$this->textValidate($text)) {
            throw new PostException(Validator::getError());
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

    private function titleValidate(string $title): bool
    {
        if (!Validator::check('Заголовок', $title, [
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

    private function textValidate(string $text): bool
    {
        if (!Validator::check('Содержимое поста', $text, [
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
