<?php

namespace NW;

class Csrf
{
    /**
     * Создает и возвращает CSRF-токен для формы.
     *
     * Это самый простой вариант защиты от CSRF-атак, когда создается один простой токен на всю длину сессии.
     * В будущем защиту от CSRF нужно будет улучшить.
     *
     * @return string
     */
    public static function getCsrfToken(): string
    {
        $token = Session::getParam('csrf');

        if (!isset($token)) {
            $string = Tools::getRandStr();
            Session::setParam('csrf', $string);
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
    public static function checkCsrfToken(string $token): bool
    {
        if (!Session::checkParam('csrf')) {
            return false;
        }

        if (hash_equals(Session::getParam('csrf'), $token)) {
            return true;
        }

        return false;
    }
}
