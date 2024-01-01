<?php

declare(strict_types=1);

namespace NW\Request;

class ServerRequestFactory
{
    /**
     * Создает объект Request на основе полученных данных или суперглобальных переменных
     *
     * TODO Для минимизации количества подгружаемых файлов и максимализации производительности метод стоит
     * TODO вынести в сам Request
     *
     * @param array $server
     * @param array $body
     * @param array $cookies
     * @param array $query
     * @param array $files
     * @return Request
     */
    public static function fromGlobals(
        array $server = [],
        array $body = [],
        array $cookies = [],
        array $query = [],
        array $files = []
    ): Request
    {
        return new Request(
            $server ?: $_SERVER,
            $body ?: $_POST,
            $cookies ?: $_COOKIE,
            $query ?: $_GET,
            $files ?: $_FILES
        );
    }
}
