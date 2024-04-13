<?php

declare(strict_types=1);

namespace NW;

class Response
{
    public const OK                              = 200;
    public const CREATED                         = 201;
    public const MOVED_PERMANENTLY               = 301;
    public const FOUND                           = 302;
    public const BAD_REQUEST                     = 400;
    public const UNAUTHORIZED                    = 401;
    public const PAYMENT_REQUIRED                = 402;
    public const FORBIDDEN                       = 403;
    public const NOT_FOUND                       = 404;
    public const METHOD_NOT_ALLOWED              = 405;
    public const INTERNAL_SERVER_ERROR           = 500;
    public const BAD_GATEWAY                     = 502;

    public const DEFAULT_500_ERROR  = '500: Internal Server Error';
    public const DEFAULT_404_ERROR  = '404: Page not found';

    public const ERROR_INVALID_CODE         = 'Invalid response code specified';
    public const ERROR_INVALID_HTTP_HEADER  = 'HTTP-header must be string';
    public const ERROR_INVALID_HEADER_CHARS = 'Invalid characters in HTTP-header';
    public const ERROR_INVALID_HEADER_VALUE = 'HTTP-header value can only be string or a number';

    /**
     * @var string - Тело ответа
     */
    private string $body;

    /**
     * @var array - Массив заголовков в формате key => value
     */
    private array $headers = [];

    /**
     * @var int - Статус ответа
     */
    private int $statusCode;

    /**
     * @var string - Текстовое описание статуса ответа
     */
    private string $reasonPhrase;

    /**
     * @var string - Версия HTTP протокола
     */
    private string $protocol = '1.1';

    /**
     * Упрощенный список кодов ответа
     *
     * @var array - Допустимые значения статуса и соответствующие им текстовые описания
     */
    private static array $phrases = [
        self::OK                    => 'OK',
        self::MOVED_PERMANENTLY     => 'Moved Permanently',
        self::FOUND                 => 'Found',
        self::UNAUTHORIZED          => 'Unauthorized',
        self::FORBIDDEN             => 'Forbidden',
        self::NOT_FOUND             => 'Not Found',
        self::METHOD_NOT_ALLOWED    => 'Method Not Allowed',
        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
    ];

    /**
     * Создаем объект Response на основе указанного тела запроса и кода ответа.
     *
     * @param string $body
     * @param int $status
     * @throws AppException
     */
    public function __construct(string $body = '', int $status = self::OK)
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
     * Устанавливает тело ответа
     *
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
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
     * Устанавливает код ответа и соответствующее ему текстовое описание
     *
     * @param int $status
     * @return Response
     * @throws AppException
     */
    public function setStatusCode(int $status): self
    {
        if (empty(self::$phrases[$status])) {
            throw new AppException(self::ERROR_INVALID_CODE);
        }

        $this->statusCode = $status;
        $this->reasonPhrase = self::$phrases[$status];

        return $this;
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
     * PSR-стандарт Response поддерживает установку заголовков в формате key => [value, value, value]
     * В моем упрощенном Response поддерживается только вариант key => value
     *
     * @param $header
     * @param $value
     * @return Response
     * @throws AppException
     */
    public function withHeader($header, $value): self
    {
        $this->validateHeaderName($header);
        $this->validateHeaderValue($value);

        $this->headers[$header] = $value;

        return $this;
    }

    /**
     * Возвращает версию HTTP-протокола
     *
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    /**
     * Проверяет корректность имени заголовка
     *
     * @param $name
     * @throws AppException
     */
    private function validateHeaderName($name): void
    {
        if (!is_string($name)) {
            throw new AppException(self::ERROR_INVALID_HTTP_HEADER);
        }
        if (!preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/', $name)) {
            throw new AppException(self::ERROR_INVALID_HEADER_CHARS);
        }
    }

    /**
     * Проверяет корректность значения заголовка
     *
     * Валидация сделана по аналогии с:
     * https://github.com/zendframework/zend-diactoros/blob/master/src/HeaderSecurity.php#L104
     *
     * @param $value
     * @throws AppException
     */
    private function validateHeaderValue($value): void
    {
        if (!is_string($value) && !is_numeric($value)) {
            throw new AppException(self::ERROR_INVALID_HEADER_VALUE);
        }

        // Look for:
        // \n not preceded by \r, OR
        // \r not followed by \n, OR
        // \r\n not followed by space or horizontal tab; these are all CRLF attacks

        // Non-visible, non-whitespace characters
        // 9 === horizontal tab
        // 10 === line feed
        // 13 === carriage return
        // 32-126, 128-254 === visible
        // 127 === DEL (disallowed)
        // 255 === null byte (disallowed)

        if (preg_match("#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#", $value) ||
            preg_match('/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/', $value)) {
            throw new AppException(self::ERROR_INVALID_HEADER_VALUE);
        }
    }
}
