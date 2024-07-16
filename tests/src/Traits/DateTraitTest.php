<?php

declare(strict_types=1);

namespace Tests\src\Traits;

use DateTime;
use WalkWeb\NW\Traits\DateTrait;
use Tests\AbstractTest;

class DateTraitTest extends AbstractTest
{
    use DateTrait;

    // Тесты на различные типы дат

    public function testDateGetElapsedTimeNow(): void
    {
        self::assertEquals('только что', self::getElapsedTime(new DateTime()));
    }

    public function testDateGetElapsedTimeMinuteAgo(): void
    {
        self::assertEquals('1 минуту назад', self::getElapsedTime(new DateTime('-1 minutes')));
        self::assertEquals('2 минуты назад', self::getElapsedTime(new DateTime('-2 minutes')));
        self::assertEquals('3 минуты назад', self::getElapsedTime(new DateTime('-3 minutes')));
        self::assertEquals('4 минуты назад', self::getElapsedTime(new DateTime('-4 minutes')));
        self::assertEquals('5 минут назад', self::getElapsedTime(new DateTime('-5 minutes')));
        self::assertEquals('6 минут назад', self::getElapsedTime(new DateTime('-6 minutes')));
        self::assertEquals('7 минут назад', self::getElapsedTime(new DateTime('-7 minutes')));
        self::assertEquals('8 минут назад', self::getElapsedTime(new DateTime('-8 minutes')));
        self::assertEquals('9 минут назад', self::getElapsedTime(new DateTime('-9 minutes')));
        self::assertEquals('10 минут назад', self::getElapsedTime(new DateTime('-10 minutes')));
        self::assertEquals('11 минут назад', self::getElapsedTime(new DateTime('-11 minutes')));
        self::assertEquals('15 минут назад', self::getElapsedTime(new DateTime('-15 minutes')));
        self::assertEquals('20 минут назад', self::getElapsedTime(new DateTime('-20 minutes')));
        self::assertEquals('22 минуты назад', self::getElapsedTime(new DateTime('-22 minutes')));
    }

    public function testDateGetElapsedTimeHourAgo(): void
    {
        self::assertEquals('1 час назад', self::getElapsedTime(new DateTime('-1 hours')));
        self::assertEquals('2 часа назад', self::getElapsedTime(new DateTime('-2 hours')));
        self::assertEquals('3 часа назад', self::getElapsedTime(new DateTime('-3 hours')));
        self::assertEquals('4 часа назад', self::getElapsedTime(new DateTime('-4 hours')));
        self::assertEquals('5 часов назад', self::getElapsedTime(new DateTime('-5 hours')));
        self::assertEquals('6 часов назад', self::getElapsedTime(new DateTime('-6 hours')));
        self::assertEquals('7 часов назад', self::getElapsedTime(new DateTime('-7 hours')));
        self::assertEquals('8 часов назад', self::getElapsedTime(new DateTime('-8 hours')));
        self::assertEquals('9 часов назад', self::getElapsedTime(new DateTime('-9 hours')));
        self::assertEquals('10 часов назад', self::getElapsedTime(new DateTime('-10 hours')));
        self::assertEquals('11 часов назад', self::getElapsedTime(new DateTime('-11 hours')));
        self::assertEquals('15 часов назад', self::getElapsedTime(new DateTime('-15 hours')));
        self::assertEquals('20 часов назад', self::getElapsedTime(new DateTime('-20 hours')));
        self::assertEquals('22 часа назад', self::getElapsedTime(new DateTime('-22 hours')));
    }

    public function testDateGetElapsedTimeDayAgo(): void
    {
        self::assertEquals('1 день назад', self::getElapsedTime(new DateTime('-1 days')));
        self::assertEquals('2 дня назад', self::getElapsedTime(new DateTime('-2 days')));
        self::assertEquals('3 дня назад', self::getElapsedTime(new DateTime('-3 days')));
        self::assertEquals('4 дня назад', self::getElapsedTime(new DateTime('-4 days')));
        self::assertEquals('5 дней назад', self::getElapsedTime(new DateTime('-5 days')));
        self::assertEquals('6 дней назад', self::getElapsedTime(new DateTime('-6 days')));
        self::assertEquals('7 дней назад', self::getElapsedTime(new DateTime('-7 days')));
        self::assertEquals('8 дней назад', self::getElapsedTime(new DateTime('-8 days')));
        self::assertEquals('9 дней назад', self::getElapsedTime(new DateTime('-9 days')));
        self::assertEquals('10 дней назад', self::getElapsedTime(new DateTime('-10 days')));
        self::assertEquals('11 дней назад', self::getElapsedTime(new DateTime('-11 days')));
        self::assertEquals('15 дней назад', self::getElapsedTime(new DateTime('-15 days')));
        self::assertEquals('20 дней назад', self::getElapsedTime(new DateTime('-20 days')));
        self::assertEquals('22 дня назад', self::getElapsedTime(new DateTime('-22 days')));
    }

    /**
     * В високосные годы с февралем до 29 января корректный расчет дат начинает ломаться
     *
     * Что с этим делать непонятно - ошибка в самом базовом коде php
     *
     * Впрочем для пользователя ошибка не так критична, по этому оставляется как есть
     */
    public function testDateGetElapsedTimeMountAgo(): void
    {
        self::assertEquals('1 месяц назад', self::getElapsedTime(new DateTime('-1 month')));
        self::assertEquals('2 месяца назад', self::getElapsedTime(new DateTime('-2 month')));
        self::assertEquals('3 месяца назад', self::getElapsedTime(new DateTime('-3 month')));
        self::assertEquals('4 месяца назад', self::getElapsedTime(new DateTime('-4 month')));
        self::assertEquals('5 месяцев назад', self::getElapsedTime(new DateTime('-5 month')));
        self::assertEquals('6 месяцев назад', self::getElapsedTime(new DateTime('-6 month')));
        self::assertEquals('7 месяцев назад', self::getElapsedTime(new DateTime('-7 month')));
        self::assertEquals('8 месяцев назад', self::getElapsedTime(new DateTime('-8 month')));
        self::assertEquals('9 месяцев назад', self::getElapsedTime(new DateTime('-9 month')));
        self::assertEquals('10 месяцев назад', self::getElapsedTime(new DateTime('-10 month')));
        self::assertEquals('11 месяцев назад', self::getElapsedTime(new DateTime('-11 month')));
    }

    public function testDateGetElapsedTimeYearAgo(): void
    {
        self::assertEquals('1 год назад', self::getElapsedTime(new DateTime('-1 year')));
        self::assertEquals('2 года назад', self::getElapsedTime(new DateTime('-2 year')));
        self::assertEquals('3 года назад', self::getElapsedTime(new DateTime('-3 year')));
        self::assertEquals('4 года назад', self::getElapsedTime(new DateTime('-4 year')));
        self::assertEquals('5 лет назад', self::getElapsedTime(new DateTime('-5 year')));
        self::assertEquals('6 лет назад', self::getElapsedTime(new DateTime('-6 year')));
        self::assertEquals('7 лет назад', self::getElapsedTime(new DateTime('-7 year')));
        self::assertEquals('8 лет назад', self::getElapsedTime(new DateTime('-8 year')));
        self::assertEquals('9 лет назад', self::getElapsedTime(new DateTime('-9 year')));
        self::assertEquals('10 лет назад', self::getElapsedTime(new DateTime('-10 year')));
        self::assertEquals('11 лет назад', self::getElapsedTime(new DateTime('-11 year')));
        self::assertEquals('15 лет назад', self::getElapsedTime(new DateTime('-15 year')));
        self::assertEquals('20 лет назад', self::getElapsedTime(new DateTime('-20 year')));
        self::assertEquals('22 года назад', self::getElapsedTime(new DateTime('-22 year')));
    }

    // Тесты на склонение

    public function testDateGetElapsedTimeTwoYearAgo(): void
    {
        self::assertEquals('2 года назад', self::getElapsedTime(new DateTime('-2 year')));
    }

    public function testDateGetElapsedTimeThreeSevenAgo(): void
    {
        self::assertEquals('7 лет назад', self::getElapsedTime(new DateTime('-7 year')));
    }

    public function testDateGetElapsedTimeFifteenYearAgo(): void
    {
        self::assertEquals('15 лет назад', self::getElapsedTime(new DateTime('-15 year')));
    }

    // Fail-тесты

    public function testDateInvalidDate(): void
    {
        self::assertEquals('Некорректная дата', self::plural(-123, 'year'));
    }

    public function testDateInvalidType(): void
    {
        self::assertEquals('Некорректный тип', self::plural(5, 'aaaaa'));
    }
}
