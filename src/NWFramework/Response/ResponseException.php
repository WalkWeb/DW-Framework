<?php

declare(strict_types=1);

namespace NW\Response;

use Exception;

class ResponseException extends Exception
{
    public const INCORRECT_STATUS_CODE         = 'Указан некорректный код ответа';
    public const HTTP_HEADER_INCORRECT_TYPE    = 'HTTP заголовок должен быть строкой';
    public const HTTP_HEADER_FORBIDDEN_SYMBOLS = 'Недопустимые символы в HTTP заголовке';
    public const HEADER_VALUE_INCORRECT_TYPE   = 'Значение HTTP заголовка может быть только строкой или числом';
    public const INCORRECT_HEADER_VALUE        = 'Некорректный формат значения HTTP заголовка';
}
