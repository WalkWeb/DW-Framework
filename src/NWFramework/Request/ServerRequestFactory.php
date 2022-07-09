<?php

declare(strict_types=1);

namespace NW\Request;

class ServerRequestFactory
{
    /**
     * Создает объект Request на основе полученных данных или суперглобальных переменных
     *
     * TODO Возможно, для минимизации количества подгружаемых файлов и максимализации производительности метод стоит
     * TODO вынести в сам Request
     *
     * @param array|null $server
     * @param array|null $body
     * @param array|null $cookies
     * @param array|null $query
     * @param array|null $files
     * @return Request
     */
    public static function fromGlobals(
        ?array $server = null,
        ?array $body = null,
        ?array $cookies = null,
        ?array $query = null,
        ?array $files = null
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
