<?php

namespace NW;

use Exception;
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
            // TODO Получать корневую директорию с вьюхами из контейнера
            // TODO А так как для этого нужен контейнер - можно создавать 500 response в App
            $view = __DIR__ . '/../../views/default/errors/500.php';
            $content = file_exists($view) ? file_get_contents($view) : '500: Internal Server Error';
            $response = new Response($content, HttpCode::INTERNAL_SERVER_ERROR);
        }

        App::emit($response);
    }
}
