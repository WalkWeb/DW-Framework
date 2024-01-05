<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Traits;

use Exception;
use NW\Traits\StringTrait;
use Tests\AbstractTestCase;

class StringTraitTest extends AbstractTestCase
{
    use StringTrait;

    /**
     * Тест на генерацию случайной строки указанной длинны
     *
     * @throws Exception
     */
    public function testToolsGetRandStr(): void
    {
        $length = 5;
        $string = self::generateString($length);

        self::assertIsString($string);
        self::assertEquals($length, mb_strlen($string));

        $length = 25;
        $string = self::generateString($length);

        self::assertIsString($string);
        self::assertEquals($length, mb_strlen($string));
    }
}
