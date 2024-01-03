<?php

namespace NW;

use DateTime;

class DateService
{
    private const SUFFIX  = 'назад';
    private const NOW     = 'только что';

    private const YEAR    = 'year';
    private const MONTHS  = 'months';
    private const DAYS    = 'days';
    private const HOURS   = 'hours';
    private const MINUTES = 'minutes';

    private const INCORRECT_DATE = 'Некорректная дата';
    private const INCORRECT_TYPE = 'Некорректный тип';

    public static array $year = [
        'год', 'года', 'лет',
    ];

    public static array $months = [
        'месяц', 'месяца', 'месяцев',
    ];

    public static array $days = [
        'день', 'дня', 'дней',
    ];

    public static array $hours = [
        'час', 'часа', 'часов',
    ];

    public static array $minutes = [
        'минута', 'минуты', 'минут',
    ];

    /**
     * Возвращает текстовую разницу между указанной датой и текущим моментом
     *
     * Пример использования:
     * \NW\DateService::getElapsedTime(\DateTime::createFromFormat('Y-m-d H:i:s', $account->getRegisterDate()))
     *
     * TODO Доработать: "1 минута назад" => "1 минуту назад"
     *
     * @param DateTime $data
     * @return string
     */
    public static function getElapsedTime(DateTime $data): string
    {
        $now = new DateTime();
        $dateInterval = $now->diff($data);

        $dateMap = ['y' => self::YEAR, 'm' => self::MONTHS, 'd' => self::DAYS, 'h' => self::HOURS, 'i' => self::MINUTES];

        foreach ($dateMap as $short => $full) {
            if ($dateInterval->$short > 0) {
                return $dateInterval->$short . ' ' . self::plural($dateInterval->$short, $full) . ' ' . self::SUFFIX;
            }
        }

        return self::NOW;
    }

    /**
     * Возвращает корректное название даты, на основе значения даты и типа даты
     *
     * @param int $value
     * @param string $type
     * @return string
     */
    public static function plural(int $value, string $type): string
    {
        if ($value < 0) {
            return self::INCORRECT_DATE;
        }

        if (!property_exists(__CLASS__, $type)) {
            return self::INCORRECT_TYPE;
        }

        $value %= 100;

        if ($value >= 10 && $value <= 20) {
            return self::$$type[2];
        }

        $value %= 10;

        if ($value === 1) {
            return self::$$type[0];
        }
        if ($value > 1 && $value < 5) {
            return self::$$type[1];
        }

        return self::$$type[2];
    }
}
