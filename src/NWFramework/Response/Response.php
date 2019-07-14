<?php

namespace NW\Response;

class Response
{
    /**
     * @var string - Тело ответа
     */
    private $body;

    /**
     * @var array - Массив заголовков в формате key => value
     */
    private $headers = [];

    /**
     * @var int - Статус ответа
     */
    private $statusCode;

    /**
     * @var string - Текстовое описание статуса ответа
     */
    private $reasonPhrase;

    /**
     * @var string - Версия HTTP протокола
     */
    private $protocol = '1.1';

    /**
     * Полный список кодов ответа: https://github.com/zendframework/zend-diactoros/blob/master/src/Response.php
     *
     * @var array - Допустимые значения статуса и соответствующие им текстовые описания
     */
    private static $phrases = [
        200 => 'OK',
        301 => 'Moved Permanently',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
    ];

    /**
     * Создаем объект Response на основе указанного тела запроса и кода ответа.
     * При необходимости можно сразу передать массив с заголовками.
     *
     * @param string $body
     * @param int $status
     */
    public function __construct(string $body = '', int $status = 200)
    {
        $this->body = $body;
        $this->setStatusCode($status);
    }

    /**
     * Возвращает тело ответа (html-контент)
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Возвращает код ответа
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Возвращает текстовое описание для кода ответа
     *
     * @return string
     */
    public function getReasonPhase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * Возвращает массив HTTP-заголовков
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Устанавливает код ответа и соответствующее ему текстовое описание
     *
     * @param int $status
     */
    public function setStatusCode(int $status): void
    {
        if (empty(self::$phrases[$status])) {
            die('Exception - Указан некорректный код ответа');
        }

        $this->statusCode = $status;
        $this->reasonPhrase = self::$phrases[$status];
    }

    /**
     * PSR-стандарт Response поддерживает установку заголовков в формате key => [value, value, value]
     * В моем упрощенном Response поддерживается только вариант key => value
     *
     * @param $header
     * @param $value
     */
    public function withHeader($header, $value): void
    {
        $this->validateHeaderName($header);
        $this->validateHeaderValue($value);

        $this->headers[$header] = $value;
    }

    /**
     * Возвращает версию HTTP-протокола
     *
     * @return string
     */
    public function getProtocolVersion() : string
    {
        return $this->protocol;
    }

    /**
     * Проверяет корректность имени заголовка
     *
     * @param $name
     */
    private function validateHeaderName($name): void
    {
        if (!is_string($name)) {
            die('HTTP заголовок должен быть строкой');
        }
        if (!preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/', $name)) {
            die('Недопустимые символы в HTTP заголовке');
        }
    }

    /**
     * Проверяет корректность значения заголовка
     *
     * @param $value
     */
    private function validateHeaderValue($value): void
    {
        if (! is_string($value) && ! is_numeric($value)) {
            die('Значение HTTP заголовка может быть только строкой или числом');
        }
        if (preg_match("#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#", $value) ||
            preg_match('/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/', $value)) {
            die('Некорректный формат значения HTTP заголовка');
        }
    }
}
