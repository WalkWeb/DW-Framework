<?php

namespace NW;

class Logs
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
        if (LOGS && LOGS_FILE) {
            self::saveToFile($log);
        }
        if (LOGS) {
            self::$logs .= "&bull; $log<br />";
        }
    }

    /**
     * Возвращает логи
     *
     * @return string
     */
    public static function getLogs(): string
    {
        return LOGS ? self::$logs : null;
    }

    /**
     * Возвращает логи корректны для передачи по JSON
     */
    public static function getJsonLogs(): ?string
    {
        return LOGS ? str_replace('"', '\\"', self::$logs) : null;
    }

    /**
     * Сохраняет лог в файл
     *
     * @param $log
     */
    protected static function saveToFile($log): void
    {
        $file = fopen(self::$dir . 'mytestlog', 'a+');
        fwrite($file, $log . "\n");
        fclose($file);
    }
}
