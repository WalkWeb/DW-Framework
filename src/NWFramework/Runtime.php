<?php

namespace NW;

// TODO Уйти от статики

class Runtime
{
    /**
     * Время точки отсчета
     *
     * @var float
     */
    private static $startTime;

    /**
     * Затраченная память с точки отсчета
     *
     * @var int
     */
    private static $startMemory;

    /**
     * Затраченное время на выполнение
     *
     * @var float
     */
    private static $runtime;

    /**
     * Количество байт памяти затраченной на выполнение
     *
     * @var int
     */
    private static $memoryCost;

    /**
     * Засекает время и расход памяти
     */
    public static function start(): void
    {
        self::$startTime = microtime(true);
        self::$startMemory = memory_get_peak_usage();
    }

    /**
     * Возвращает результат: сколько времени выполнялся скрипт и максимальный расход памяти во время выполнения
     *
     * TODO Уйти от html в коде
     *
     * @return string
     */
    public static function end(): string
    {
        return '<hr color="#444">
                <p>
                Время вывода страницы: ' . self::getRuntime() . ' ms<br />
                Расход памяти: ' . self::getMemoryCostClipped() . '
                </p>';
    }

    /**
     * Возвращает количество времени затраченного на выполнение в миллисекундах
     *
     * @return float
     */
    public static function getRuntime(): float
    {
        if (self::$runtime === null) {
            self::$runtime = round((microtime(true) - self::$startTime) * 1000, 2);
        }

        return self::$runtime;
    }

    /**
     * Возвращает количество памяти затраченной на выполнение
     *
     * @return int
     */
    public static function getMemoryCost(): int
    {
        if (self::$memoryCost === null) {
            self::$memoryCost = memory_get_peak_usage() - self::$startMemory;
        }

        return self::$memoryCost;
    }

    /**
     * Возвращает количество памяти затраченной на выполнение в удобном виде для восприятия
     *
     * @return string
     */
    public static function getMemoryCostClipped(): string
    {
        return self::convert(self::getMemoryCost());
    }

    /**
     * Конвертирует размер памяти в байтах в более удобный вид для восприятия
     *
     * @param $size
     * @return string
     */
    private static function convert($size): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        $i = (int)floor(log($size, 1024));
        return round($size / 1024 ** $i, 2) . ' ' . $unit[$i];
    }
}
