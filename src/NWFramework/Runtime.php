<?php

namespace NW;

use NW\Connection;

class Runtime
{
    private static $time;
    private static $memory;

    /**
     * Засекает время и расход памяти
     */
    public static function start(): void
    {
        self::$time = microtime(true);
        self::$memory = memory_get_peak_usage();
    }

    /**
     * Возвращает результат: сколько времени выполнялся скрипт и максимальный расход памяти во время выполнения
     *
     * @return string
     */
    public static function end(): string
    {
        $db = Connection::getInstance();

        return '<p>
                Время вывода страницы: ' . round((microtime(true) - self::$time) * 1000, 2) . ' ms<br />
                Расход памяти: ' . self::convert(memory_get_peak_usage() - self::$memory) . '<br />
                Количество запросов: ' . $db->getQueryNumber() . '
                </p>';
    }

    private static function convert($size): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        $i = (int)floor(log($size, 1024));
        return @round($size / 1024**$i, 2) . ' ' . $unit[$i];
    }
}
