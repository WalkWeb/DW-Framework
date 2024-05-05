<?php

namespace WalkWeb\NW;

use Exception;
use Throwable;

/**
 * Для минимизации количества подгружаемых файлов делается один класс исключений на все ошибки уровня фреймворка
 *
 * @package WalkWeb\NW
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
    public function __construct(string $message, $code = Response::INTERNAL_SERVER_ERROR, AppException $previous = null)
    {
        set_exception_handler([$this, 'printException']);
        parent::__construct($message, $code, $previous);
    }

    /**
     * Выводим сообщение об ошибке. Если сайт не в режиме разработчика - отдается 500 ошибка
     *
     * @param Throwable $e
     * @param string|null $appEnv
     * @throws AppException
     */
    public function printException(Throwable $e, ?string $appEnv = null): void
    {
        $appEnv = $appEnv ?? APP_ENV;

        if ($appEnv === Container::APP_DEV) {
            $response = new Response(
                '<h1>Ошибка</h1><p>Ошибка [' . $this->code . ']: ' . $e->getMessage() . '<br />Файл: ' . $e->getFile() . '<br />Строка: ' . $e->getLine() . '</p>',
                $this->code
            );
        } else {
            $response = App::createInternalErrorResponse();
        }

        App::emit($response);
    }
}
