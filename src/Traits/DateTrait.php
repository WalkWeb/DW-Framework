<?php

namespace WalkWeb\NW\Traits;

use DateTime;

trait DateTrait
{
    private static string $suffix  = 'назад';
    private static string $now     = 'только что';

    private static string $incorrectData = 'Некорректная дата';
    private static string $incorrectType = 'Некорректный тип';

    public static array $years = [
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
        'минуту', 'минуты', 'минут',
    ];

    /**
     * Возвращает текстовую разницу между указанной датой и текущим моментом
     *
     * Пример использования:
     * getElapsedTime(\DateTime::createFromFormat('Y-m-d H:i:s', $account->getRegisterDate()))
     *
     * @param DateTime $data
     * @return string
     */
    public function getElapsedTime(DateTime $data): string
    {
        $now = new DateTime();
        $dateInterval = $now->diff($data);

        $dateMap = ['y' => 'years', 'm' => 'months', 'd' => 'days', 'h' => 'hours', 'i' => 'minutes'];

        foreach ($dateMap as $short => $full) {
            if ($dateInterval->$short > 0) {
                return $dateInterval->$short . ' ' . $this->plural($dateInterval->$short, $full) . ' ' . self::$suffix;
            }
        }

        return self::$now;
    }

    /**
     * Возвращает корректное название даты, на основе значения даты и типа даты
     *
     * @param int $value
     * @param string $type
     * @return string
     */
    public function plural(int $value, string $type): string
    {
        if ($value < 0) {
            return self::$incorrectData;
        }

        if (!property_exists(__CLASS__, $type)) {
            return self::$incorrectType;
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
