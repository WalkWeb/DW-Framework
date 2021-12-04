<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Tools;
use Tests\AbstractTestCase;

class ToolsTest extends AbstractTestCase
{
    public function testToolsGetRandStr(): void
    {
        $length = 5;
        $string = Tools::getRandStr($length);

        self::assertIsString($string);
        self::assertEquals($length, mb_strlen($string));

        $length = 25;
        $string = Tools::getRandStr($length);

        self::assertIsString($string);
        self::assertEquals($length, mb_strlen($string));
    }

    public function testToolsRand(): void
    {
        $min = 10;
        $max = 100;

        $int = Tools::rand($min, $max);

        self::assertTrue($int >= $min && $int <= $max);
    }

    public function testToolsGetNowDateTime(): void
    {
        self::assertEquals(date('Y-m-d H:i:s'), Tools::getNowDateTime());
    }
}
