<?php

namespace NW;

// TODO Переделать на работу с Request / Response

class Session
{
    /**
     * Устанавливает ключ => значение в сессию
     *
     * @param $key
     * @param $value
     */
    public static function setParam($key, $value): void
    {
        self::start();

        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return string|null
     */
    public static function getParam($key): ?string
    {
        self::start();

        if (empty($_SESSION[$key])) {
            return null;
        }

        return $_SESSION[$key];
    }

    /**
     * Проверяет, есть ли указанный параметр в сессии
     *
     * @param $key
     * @return bool
     */
    public static function checkParam($key): bool
    {
        self::start();

        return !empty($_SESSION[$key]);
    }

    /**
     * Запускает сессию, если она еще не запущена
     */
    private static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
