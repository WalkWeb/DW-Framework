<?php

declare(strict_types=1);

namespace Tests\src\Traits;

use Exception;
use Tests\AbstractTest;

class StringTraitTest extends AbstractTest
{
    /**
     * Тест на генерацию случайной строки указанной длинны
     *
     * @throws Exception
     */
    public function testStringTraitGetRandStr(): void
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

    /**
     * Тесты на транслитерацию кириллицы в латиницу
     */
    public function testStringTraitTransliterate(): void
    {
        self::assertEquals(
            'Pered-nachalom-ustanovki-vy-dolzhny-znat-dlya-chego-vy-hotite-ispolzovat-PHP',
            self::transliterate('Перед началом установки вы должны знать, для чего вы хотите использовать PHP.')
        );
        self::assertEquals(
            'Sozdavat-veb-sayty-i-veb-prilozheniya-(Skripty-na-storone-servera)',
            self::transliterate('Создавать веб-сайты и веб-приложения (Скрипты на стороне сервера)')
        );
        self::assertEquals(
            'V-sluchae-ustanovki-servera-i-PHP-samostoyatelno',
            self::transliterate('В случае установки сервера и PHP самостоятельно')
        );
    }
}
