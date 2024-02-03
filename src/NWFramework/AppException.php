<?php

namespace NW;

use Exception;
use NW\App\App;
use NW\Response\Response;
use NW\Utils\HttpCode;
use Throwable;

/**
 * Для минимизации количества подгружаемых файлов делается один класс исключений на все ошибки уровня фреймворка
 *
 * @package NW
 */
class AppException extends Exception
{
    /**
     * Создает сообщение об ошибке
     *
     * @param string $message
     * @param int $code
     * @param AppException|null $previous
     */
    public function __construct(string $message, $code = HttpCode::INTERNAL_SERVER_ERROR, AppException $previous = null)
    {
        set_exception_handler([$this, 'printException']);
        parent::__construct($message, $code, $previous);
    }

    /**
     * Выводим сообщение об ошибке. Если сайт не в режиме разработчика - отдается 404 ошибка
     *
     * TODO 500 ошибка не в режиме разработчика
     *
     * TODO Логика разной обработки исключений будет делаться в public/index.php
     *
     * @param Throwable $e
     * @throws AppException
     */
    public function printException(Throwable $e): void
    {
        if (DEV) {
            $response = new Response(
                '<h1>Ошибка</h1><p>Ошибка [' . $this->code . ']: ' . $e->getMessage() . '<br />Файл: ' . $e->getFile() . '<br />Строка: ' . $e->getLine() . '</p>',
                $this->code
            );
        } else {
            $response = new Response($e->getMessage(), $this->code);
        }

        App::emit($response);
    }
}
