<?php

declare(strict_types=1);

namespace Tests\src\Traits;

use Exception;
use Tests\AbstractTest;
use WalkWeb\NW\AppException;

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
            'Pered nachalom ustanovki vy dolzhny znat, dlya chego vy hotite ispolzovat PHP.',
            self::transliterate('Перед началом установки вы должны знать, для чего вы хотите использовать PHP.')
        );
        self::assertEquals(
            'Sozdavat veb-sayty i veb-prilozheniya (Skripty na storone servera)',
            self::transliterate('Создавать веб-сайты и веб-приложения (Скрипты на стороне сервера)')
        );
        self::assertEquals(
            'V sluchae ustanovki servera i PHP samostoyatelno',
            self::transliterate('В случае установки сервера и PHP самостоятельно')
        );
        self::assertEquals(
            'Diablo 2: Resurrected — Runnye slova (Runewords)',
            self::transliterate('Diablo 2: Resurrected — Рунные слова (Runewords)')
        );
    }

    /**
     * @dataProvider jsonEncodeDataProvider
     * @param array $data
     * @param string $expectedJson
     * @throws AppException
     */
    public function testStringTraitJsonEncodeSuccess(array $data, string $expectedJson): void
    {
        self::assertEquals($expectedJson, self::jsonEncode($data));
    }

    /**
     * @dataProvider jsonDecodeDataProvider
     * @param string $json
     * @param array $expectedArray
     * @throws AppException
     */
    public function testStringTraitJsonDecodeSuccess(string $json, array $expectedArray): void
    {
        self::assertEquals($expectedArray, self::jsonDecode($json));
    }

    public function testStringTraitJsonDecodeFail(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('json_decode: Syntax error');
        self::jsonDecode('invalid_json');
    }

    /**
     * @return array
     */
    public function jsonEncodeDataProvider(): array
    {
        return [
            [
                [
                    'slug' => 'slug',
                    'name' => 'name',
                ],
                '{"slug":"slug","name":"name"}',
            ],
            [
                [
                    [
                        'slug' => 'slug-1',
                        'name' => 'name 1',
                    ],
                    [
                        'slug' => 'slug-2',
                        'name' => 'name 2',
                    ],
                ],
                '[{"slug":"slug-1","name":"name 1"},{"slug":"slug-2","name":"name 2"}]',
            ],
        ];
    }

    /**
     * @return array
     */
    public function jsonDecodeDataProvider(): array
    {
        return [
            [
                '{"slug":"slug","name":"name"}',
                [
                    'slug' => 'slug',
                    'name' => 'name',
                ],
            ],
            [
                '[{"slug":"slug-1","name":"name 1"},{"slug":"slug-2","name":"name 2"}]',
                [
                    [
                        'slug' => 'slug-1',
                        'name' => 'name 1',
                    ],
                    [
                        'slug' => 'slug-2',
                        'name' => 'name 2',
                    ],
                ],
            ],
        ];
    }
}
