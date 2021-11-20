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
     * TODO В случае отсутствия может лучше кидать exception?
     *
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
     * TODO Rename to existParam
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
        if (!self::isSessionStarted()) {
            session_start();
        }
    }

    /**
     * Проверяет, запущены ли сессии
     *
     * Если php работает в консольном режиме (cli) сессии не стартуем - это приведет к ошибке
     * "session_start(): Cannot start session when headers already sent"
     *
     * @return bool
     */
    private static function isSessionStarted(): bool
    {
        if (PHP_SAPI === 'cli') {
            return true;
        }

        if (PHP_VERSION_ID >= 50400) {
            return session_status() === PHP_SESSION_ACTIVE;
        }

        return session_id() !== '';
    }
}
