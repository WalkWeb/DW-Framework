<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use DateTime;
use NW\DateService;
use Tests\AbstractTestCase;

class DateServiceTest extends AbstractTestCase
{
    // Тесты на различные типы дат

    public function testDateGetElapsedTimeNow(): void
    {
        self::assertEquals('только что', DateService::getElapsedTime(new DateTime()));
    }

    public function testDateGetElapsedTimeOneMinuteAgo(): void
    {
        self::assertEquals('1 минута назад', DateService::getElapsedTime(new DateTime('-1 minutes')));
    }

    public function testDateGetElapsedTimeOneHourAgo(): void
    {
        self::assertEquals('1 час назад', DateService::getElapsedTime(new DateTime('-1 hours')));
    }

    public function testDateGetElapsedTimeOneDayAgo(): void
    {
        self::assertEquals('1 день назад', DateService::getElapsedTime(new DateTime('-1 days')));
    }

    public function testDateGetElapsedTimeOneMountAgo(): void
    {
        self::assertEquals('1 месяц назад', DateService::getElapsedTime(new DateTime('-1 month')));
    }

    public function testDateGetElapsedTimeOneYearAgo(): void
    {
        self::assertEquals('1 год назад', DateService::getElapsedTime(new DateTime('-1 year')));
    }

    // Тесты на склонение

    public function testDateGetElapsedTimeTwoYearAgo(): void
    {
        self::assertEquals('2 года назад', DateService::getElapsedTime(new DateTime('-2 year')));
    }

    public function testDateGetElapsedTimeThreeSevenAgo(): void
    {
        self::assertEquals('7 лет назад', DateService::getElapsedTime(new DateTime('-7 year')));
    }

    public function testDateGetElapsedTimeFifteenYearAgo(): void
    {
        self::assertEquals('15 лет назад', DateService::getElapsedTime(new DateTime('-15 year')));
    }

    // Fail-тесты

    public function testDateInvalidDate(): void
    {
        self::assertEquals('Некорректная дата', DateService::plural(-123, 'year'));
    }

    public function testDateInvalidType(): void
    {
        self::assertEquals('Некорректный тип', DateService::plural(5, 'aaaaa'));
    }
}
