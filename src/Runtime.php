<?php

namespace WalkWeb\NW;

class Runtime
{
    /**
     * Время точки отсчета
     *
     * @var float
     */
    private float $startTime;

    /**
     * Затраченная память с точки отсчета
     *
     * @var int
     */
    private int $startMemory;

    /**
     * Затраченное время на выполнение
     *
     * @var float|null
     */
    private ?float $runtime = null;

    /**
     * Количество байт памяти затраченной на выполнение
     *
     * @var int|null
     */
    private ?int $memoryCost = null;

    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_peak_usage();
    }

    /**
     * Возвращает результат: сколько времени выполнялся скрипт и максимальный расход памяти во время выполнения
     *
     * @return string
     */
    public function getStatistic(): string
    {
        return 'Runtime: ' . $this->getRuntime() . ' ms, memory cost: ' . $this->getMemoryCostClipped();
    }

    /**
     * Возвращает количество времени затраченного на выполнение в миллисекундах
     *
     * @return float
     */
    public function getRuntime(): float
    {
        if ($this->runtime === null) {
            $this->runtime = round((microtime(true) - $this->startTime) * 1000, 2);
        }

        return $this->runtime;
    }

    /**
     * Возвращает количество памяти затраченной на выполнение
     *
     * @return int
     */
    public function getMemoryCost(): int
    {
        if ($this->memoryCost === null) {
            $this->memoryCost = memory_get_peak_usage() - $this->startMemory;
        }

        return $this->memoryCost;
    }

    /**
     * Возвращает количество памяти затраченной на выполнение в удобном виде для восприятия
     *
     * @return string
     */
    public function getMemoryCostClipped(): string
    {
        return $this->convert($this->getMemoryCost());
    }

    /**
     * Конвертирует размер памяти в байтах в более удобный вид для восприятия
     *
     * @param $size
     * @return string
     */
    private function convert($size): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        $i = (int)floor(log($size, 1024));
        return round($size / 1024 ** $i, 2) . ' ' . $unit[$i];
    }
}
