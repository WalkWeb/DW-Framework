<?php

namespace NW\Request;

class Request
{
    /** @var string - GET | POST | PUT | DELETE | FILES */
    private $method;

    /** @var string - Если URL site.ru/blog/10, то URI будет /blog/10 */
    private $uri;

    /** @var string - Обычно это HTTP/1.1 */
    private $protocol;

    /** @var array - $_COOKIES */
    private $cookies;

    /** @var array - $_GET */
    private $query;

    /** @var array - $_POST */
    private $body;

    /** @var array - $_FILES */
    private $files;

    /** @var array - $_SERVER */
    private $server;

    /** @var array - Массив дополнительных параметров */
    private $attributes = [];

    /**
     * Получает глобальные параметры запроса и формирует из них объект запроса.
     *
     * Если нужно будет получать запросы в виде json, то нужно добавить к отслеживанию 'php://input'
     *
     * @param array $server
     * @param array $cookies
     * @param array $query
     * @param array $body
     * @param array $files
     */
    public function __construct(array $server, array $cookies, array $query, array $body, array $files)
    {
        $this->server = $server;

        // TODO Такая обработка URI позволяет принимать запросы вида /registration?ref=friend, на маршрут /registration
        // TODO Но это же приводит к возможности открывать одну и туже страницу по разным uri, что является уязвимостью
        // TODO Для SEO
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
}
