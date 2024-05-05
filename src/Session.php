<?php

namespace WalkWeb\NW;

class Session
{
    /**
     * Проверяет, запущены ли сессии
     *
     * Если php работает в консольном режиме (cli) сессии не стартуем - это приведет к ошибке
     * "session_start(): Cannot start session when headers already sent"
     *
     * @return bool
     */
    public static function existSession(): bool
    {
        if (PHP_SAPI === 'cli') {
            return true;
        }

        if (PHP_VERSION_ID >= 50400) {
            return session_status() === PHP_SESSION_ACTIVE;
        }

        return session_id() !== '';
    }

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

        if (!self::existParam($key)) {
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
    public static function existParam($key): bool
    {
        self::start();

        return !empty($_SESSION[$key]);
    }

    /**
     * Запускает сессию, если она еще не запущена
     */
    public static function start(): void
    {
        if (!self::existSession()) {
            session_start();
        }
    }

    /**
     * Завершает сессию, если она запущена
     */
    public static function end(): void
    {
        if (self::existSession()) {
            session_abort();
        }
    }
}
