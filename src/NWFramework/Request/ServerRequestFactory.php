<?php

namespace NW\Request;

class ServerRequestFactory
{
    /**
     * Создает объект Request на основе полученных данных или суперглобальных переменных
     *
     * @param null $server
     * @param null $body
     * @param null $cookies
     * @param null $query
     * @param null $files
     * @return Request
     */
    public static function fromGlobals(
        $server = null,
        $body = null,
        $cookies = null,
        $query = null,
        $files = null
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
