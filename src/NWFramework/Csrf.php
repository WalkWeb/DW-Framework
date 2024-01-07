<?php

namespace NW;

use Exception;
use NW\Traits\StringTrait;

class Csrf
{
    use StringTrait;

    public const TOKEN_NAME = 'csrf';

    /**
     * Создает и возвращает CSRF-токен для формы.
     *
     * Это самый простой вариант защиты от CSRF-атак, когда создается один простой токен на всю длину сессии.
     * В будущем защиту от CSRF нужно будет улучшить.
     *
     * @return string
     * @throws Exception
     */
    public function getCsrfToken(): string
    {
        $token = Session::getParam(self::TOKEN_NAME);

        if (!isset($token)) {
            $string = self::generateString();
            Session::setParam(self::TOKEN_NAME, $string);
        } else {
            $string = $token;
        }

        return $string;
    }

    /**
     * Проверяет CSRF-токен
     *
     * @param string $token
     * @return bool
     */
    public function checkCsrfToken(string $token): bool
    {
        if (!Session::existParam(self::TOKEN_NAME)) {
            return false;
        }

        if (hash_equals(Session::getParam(self::TOKEN_NAME), $token)) {
            return true;
        }

        return false;
    }
}
