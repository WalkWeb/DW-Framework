<?php

declare(strict_types=1);

namespace NW;

use NW\AppException;
use NW\Utils\HttpCode;

class Response
{
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
     * Полный список в HttpCode
     *
     * @var array - Допустимые значения статуса и соответствующие им текстовые описания
     */
    private static array $phrases = [
        HttpCode::OK                    => 'OK',
        HttpCode::MOVED_PERMANENTLY     => 'Moved Permanently',
        HttpCode::FOUND                 => 'Found',
        HttpCode::UNAUTHORIZED          => 'Unauthorized',
        HttpCode::FORBIDDEN             => 'Forbidden',
        HttpCode::NOT_FOUND             => 'Not Found',
        HttpCode::METHOD_NOT_ALLOWED    => 'Method Not Allowed',
        HttpCode::INTERNAL_SERVER_ERROR => 'Internal Server Error',
    ];

    /**
     * Создаем объект Response на основе указанного тела запроса и кода ответа.
     * При необходимости можно сразу передать массив с заголовками.
     *
     * @param string $body
     * @param int $status
     * @throws AppException
     */
    public function __construct(string $body = '', int $status = HttpCode::OK)
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
     * Устанавливает код ответа и соответствующее ему текстовое описание
     *
     * @param int $status
     * @throws AppException
     */
    public function setStatusCode(int $status): void
    {
        if (empty(self::$phrases[$status])) {
            throw new AppException('Указан некорректный код ответа');
        }

        $this->statusCode = $status;
        $this->reasonPhrase = self::$phrases[$status];
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
     * @throws AppException
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
            throw new AppException('HTTP заголовок должен быть строкой');
        }
        if (!preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/', $name)) {
            throw new AppException('Недопустимые символы в HTTP заголовке');
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
            throw new AppException('Значение HTTP заголовка может быть только строкой или числом');
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
            throw new AppException('Некорректный формат значения HTTP заголовка');
        }
    }
}
