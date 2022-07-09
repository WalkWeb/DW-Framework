<?php

declare(strict_types=1);

namespace NW\Request;

class Request
{
    /**
     * GET | POST | PUT | DELETE | FILES
     *
     * @var string
     */
    private $method;

    /**
     * URI
     *
     * @var string
     */
    private $uri;

    /**
     * Обычно это HTTP/1.1
     *
     * @var string
     */
    private $protocol;

    /**
     * $_COOKIES
     *
     * @var array
     */
    private $cookies;

    /**
     * $_GET
     *
     * @var array
     */
    private $query;

    /**
     * $_POST
     *
     * @var array
     */
    private $body;

    /**
     * $_FILES
     *
     * @var array
     */
    private $files;

    /**
     * $_SERVER
     *
     * @var array
     */
    private $server;

    /**
     * Массив дополнительных параметров
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Получает глобальные параметры запроса и формирует из них объект запроса.
     *
     * Если нужно будет получать запросы в виде json, то нужно добавить к отслеживанию 'php://input'
     *
     * @param array $server
     * @param array|null $body
     * @param array|null $cookies
     * @param array|null $query
     * @param array|null $files
     */
    public function __construct(array $server, ?array $body = [], ?array $cookies = [], ?array $query = [], ?array $files = [])
    {
        $this->server = $server;

        // Такая обработка URI позволяет принимать запросы вида /registration?ref=friend, на маршрут /registration
        // Но лично я подумываю отказаться от GET-параметров в URL в принципе
        $this->uri = !empty($server['REQUEST_URI']) ?
            explode('?', $server['REQUEST_URI'])[0]
            : '/';

        $this->protocol = !empty($server['SERVER_PROTOCOL']) ? $server['SERVER_PROTOCOL'] : 'HTTP/1.1';
        $this->method = !empty($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : 'GET';
        $this->cookies = $cookies;
        $this->query = $query;
        $this->body = $body;
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getServer(): array
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Возвращает массив дополнительных параметров
     *
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }

    /**
     * Устанавливает дополнительный параметр
     *
     * @param $attribute
     * @param $value
     */
    public function withAttribute($attribute, $value): void
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * Возвращает значение указанного дополнительного параметра, можно указать дефолтное значение по умолчанию, если
     * параметр не задан
     *
     * @param $attribute
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($attribute, $default = null)
    {
        if (!array_key_exists($attribute, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$attribute];
    }

    /**
     * Метод создан для более простого доступа к параметрам, которые находятся свойствах запроса
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->body[$name] ?? $this->attributes[$name] ?? $this->query[$name] ?? null;
    }

    // Функционал этих методов не нужен, но на их отсутствие ругается phpStorm
    public function __set($name, $value) {}
    public function __isset($name) {}
}
