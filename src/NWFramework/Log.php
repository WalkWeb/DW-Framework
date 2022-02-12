<?php

namespace NW;

// TODO Уйти от статики

class Log
{
    /**
     * Место хранения файлов логов
     *
     * @var string
     */
    private static $dir = DIR . '/logs/';

    /**
     * Строка с логами
     *
     * @var string
     */
    public static $logs = '<p class="logs">'; // Отладочные логи

    /**
     * Добавляет новый лог
     *
     * @param $log
     */
    public static function setLogs($log): void
    {
        if (LOGS) {
            self::$logs .= "&bull; $log<br />";

            if (LOGS_FILE) {
                self::saveToFile($log);
            }
        }
    }

    /**
     * Возвращает логи
     *
     * @return string
     */
    public static function getLogs(): string
    {
        return LOGS ? self::$logs : '';
    }

    /**
     * Возвращает логи корректны для передачи по JSON
     *
     * TODO Странный метод, уже не вспомню, для чего его делал
     */
    public static function getJsonLogs(): ?string
    {
        return LOGS ? str_replace('"', '\\"', self::$logs) : null;
    }

    /**
     * Костыль, который приходится делать из-за статического класса. Сбрасывает все записанные ранее логи
     *
     * TODO На удаление, когда статика будет удалена
     */
    public static function resetLog(): void
    {
        self::$logs = '<p class="logs">';
    }

    /**
     * Сохраняет лог в файл
     *
     * @param $log
     */
    protected static function saveToFile($log): void
    {
        $file = fopen(self::$dir . 'mytestlog', 'ab+');
        fwrite($file, $log . "\n");
        fclose($file);
    }
}
