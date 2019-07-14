<?php

namespace NW;

use NW\Response\Response;
use NW\Response\Emitter;

class Exception extends \Exception
{
    /**
     * Создает сообщение об ошибке
     *
     * Exception constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 200, Exception $previous = null)
    {
        set_exception_handler([$this, 'printException']);
        parent::__construct($message, $code, $previous);
    }

    /**
     * Выводим сообщение об ошибке. Если сайт не в режиме разработчика - отдается 404 ошибка
     *
     * @param \Throwable $e
     */
    public function printException(\Throwable $e): void
    {
        if (DEV) {
            $response = new Response(
                '<h1>Ошибка</h1><p>Ошибка [' . $this->code . ']: ' . $e->getMessage() . '<br />Файл: ' . $e->getFile() . '<br />Строка: ' . $e->getLine() . '</p>',
                $this->code
            );
        } else {
            $response = new Response($e->getMessage(), $this->code);
        }

        Emitter::emit($response);
    }
}
