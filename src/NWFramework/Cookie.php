<?php

namespace NW;

// TODO Переделать на работу с Request / Response

class Cookie
{
    /**
     * Устанавливает куки
     *
     * @param string $name
     * @param int|string $value
     * @param int $time
     */
    public static function setCookie(string $name, $value, int $time): void
    {
        setcookie($name, $value, time() + $time, '/');
    }

    /**
     * Возвращает куки по указанному имени, если они есть
     *
     * @param string $name
     * @return null|string
     */
    public static function getCookie(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Проверяет наличие куков по имени
     *
     * @param string $name
     * @return bool
     */
    public static function checkCookie(string $name): bool
    {
        return !empty($_COOKIE[$name]);
    }

    /**
     * Удаляет куки по имени
     *
     * @param string $name
     */
    public static function deleteCookie(string $name): void
    {
        setcookie($name, '', -1, '/');
    }
}
