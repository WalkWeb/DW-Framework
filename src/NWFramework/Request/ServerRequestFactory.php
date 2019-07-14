<?php

namespace NW\Request;

class ServerRequestFactory
{
    /**
     * Создает объект Request на основе полученных данных или суперглобальных переменных
     *
     * @param null $server
     * @param null $cookies
     * @param null $query
     * @param null $body
     * @param null $files
     * @return Request
     */
    public static function fromGlobals(
        $server = null,
        $cookies = null,
        $query = null,
        $body = null,
        $files = null
    ): Request
    {
        return new Request(
            $server ?: $_SERVER,
            $cookies ?: $_COOKIE,
            $query ?: $_GET,
            $body ?: $_POST,
            $files ?: $_FILES
        );
    }
}
