<?php

declare(strict_types=1);

namespace Tests\src\Traits;

use DateTime;
use Exception;
use Tests\AbstractTest;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Traits\ValidationTrait;

class ValidationTraitTest extends AbstractTest
{
    use ValidationTrait;

    /**
     * @dataProvider intMinMaxValueSuccessDataProvider
     * @param int $value
     * @param int $min
     * @param int $max
     * @throws AppException
     */
    public function testValidationIntMinMaxValueSuccess(int $value, int $min, int $max): void
    {
        self::assertEquals($value, self::intMinMaxValue($value, $min, $max, 'error'));
    }

    /**
     * @dataProvider intMinMaxValueFailDataProvider
     * @param int $value
     * @param int $min
     * @param int $max
     * @param string $error
     */
    public function testValidationIntMinMaxValueFail(int $value, int $min, int $max, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::intMinMaxValue($value, $min, $max, $error);
    }

    /**
     * @dataProvider intOrFloatSuccessDataProvider
     * @param array $data
     * @param string $filed
     * @throws AppException
     */
    public function testValidationIntOrFloatSuccess(array $data, string $filed): void
    {
        self::assertEquals($data[$filed], self::intOrFloat($data, $filed, 'error'));
    }

    /**
     * @dataProvider intOrFloatFailDataProvider
     * @param array $data
     * @param string $filed
     * @param string $error
     * @throws AppException
     */
    public function testValidationIntOrFloatFail(array $data, string $filed, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::intOrFloat($data, $filed, $error);
    }

    /**
     * @dataProvider arraySuccessDataProvider
     * @param array $data
     * @param string $filed
     * @throws AppException
     */
    public function testValidationArraySuccess(array $data, string $filed): void
    {
        self::assertEquals($data[$filed], self::array($data, $filed, 'error'));
    }

    /**
     * @dataProvider arrayFailDataProvider
     * @param array $data
     * @param string $filed
     * @param string $error
     * @throws AppException
     */
    public function testValidationArrayFail(array $data, string $filed, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::array($data, $filed, $error);
    }

    /**
     * @dataProvider stringOrNullSuccessDataProvider
     * @param array $data
     * @param string $filed
     * @param string $error
     * @param string|null $expected
     * @throws AppException
     */
    public function testValidationStringOrNullSuccess(array $data, string $filed, string $error, ?string $expected): void
    {
        self::assertEquals($expected, self::stringOrNull($data, $filed, $error));
    }

    /**
     * @dataProvider stringOrNullFailDataProvider
     * @param array $data
     * @param string $filed
     * @param string $error
     * @throws AppException
     */
    public function testValidationStringOrNullFail(array $data, string $filed, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::stringOrNull($data, $filed, $error);
    }

    /**
     * @dataProvider stringOrDefaultDataProvider
     * @param array $data
     * @param string $filed
     * @param string $default
     * @param string $expected
     */
    public function testValidationStringOrDefault(array $data, string $filed, string $default, string $expected): void
    {
        self::assertEquals($expected, self::stringOrDefault($data, $filed, $default));
    }

    /**
     * @dataProvider uuidSuccessDataProvider
     * @param array $data
     * @param string $filed
     * @param string $error
     * @throws AppException
     */
    public function testValidationUuidSuccess(array $data, string $filed, string $error): void
    {
        self::assertEquals($data[$filed], self::uuid($data, $filed, $error));
    }

    /**
     * @dataProvider uuidFailDataProvider
     * @param array $data
     * @param string $filed
     * @param string $error
     * @throws AppException
     */
    public function testValidationUuidFail(array $data, string $filed, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::uuid($data, $filed, $error);
    }

    /**
     * @dataProvider dateSuccessDataProvider
     * @param array $data
     * @param string $filed
     * @param string $error
     * @throws Exception
     */
    public function testValidationDateSuccess(array $data, string $filed, string $error): void
    {
        self::assertEquals(new DateTime($data[$filed]), self::date($data, $filed, $error));
    }

    /**
     * @dataProvider dateFailDataProvider
     * @param array $data
     * @param string $filed
     * @param string $error
     * @throws Exception
     */
    public function testValidationDateFail(array $data, string $filed, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::date($data, $filed, $error);
    }

    /**
     * @return array
     */
    public function intMinMaxValueSuccessDataProvider(): array
    {
        return [
            [
                10,
                0,
                20,
            ],
            [
                10,
                10,
                10,
            ],
            [
                10,
                9,
                11,
            ],
        ];
    }

    /**
     * @return array
     */
    public function intMinMaxValueFailDataProvider(): array
    {
        return [
            [
                30,
                0,
                20,
                'error',
            ],
            [
                5,
                6,
                10,
                'error',
            ],
            [
                11,
                6,
                10,
                'error',
            ],
        ];
    }

    /**
     * @return array
     */
    public function intOrFloatSuccessDataProvider(): array
    {
        return [
            [
                [
                    'value' => 123,
                ],
                'value',
            ],
            [
                [
                    'value' => 0,
                ],
                'value',
            ],
            [
                [
                    'value' => 12.40,
                ],
                'value',
            ],
            [
                [
                    'value' => 0.0,
                ],
                'value',
            ],
        ];
    }

    /**
     * @return array
     */
    public function intOrFloatFailDataProvider(): array
    {
        return [
            [
                [
                    'value' => null,
                ],
                'value',
                'error #1',
            ],
            [
                [
                    'value' => '0',
                ],
                'value',
                'error #2',
            ],
            [
                [
                    'value' => true,
                ],
                'value',
                'error #3',
            ],
        ];
    }

    public function arraySuccessDataProvider(): array
    {
        return [
            [
                [
                    'value' => ['...'],
                ],
                'value',
            ],
        ];
    }

    public function arrayFailDataProvider(): array
    {
        return [
            [
                [
                    'value' => null,
                ],
                'value',
                'error #1',
            ],
            [
                [
                    'value' => '{}',
                ],
                'value',
                'error #2',
            ],
        ];
    }

    public function stringOrNullSuccessDataProvider(): array
    {
        return [
            [
                [
                    'property' => 'name',
                ],
                'property',
                'error #1',
                'name',
            ],
            [
                [
                    'value' => null,
                ],
                'value',
                'error #2',
                null,
            ],
        ];
    }

    public function stringOrNullFailDataProvider(): array
    {
        return [
            [
                [
                    'property' => 123,
                ],
                'property',
                'error #1',
            ],
            [
                [],
                'property',
                'error #2',
            ],
        ];
    }

    public function stringOrDefaultDataProvider(): array
    {
        return [
            [
                [
                    'property' => 'value',
                ],
                'property',
                'default_value',
                'value',
            ],
            [
                [
                    'property' => null,
                ],
                'property',
                'default_value #1',
                'default_value #1',
            ],
            [
                [
                    'property' => 123,
                ],
                'property',
                'default_value #2',
                'default_value #2',
            ],
            [
                [],
                'property',
                'default_value #3',
                'default_value #3',
            ],
        ];
    }

    public function uuidSuccessDataProvider(): array
    {
        return [
            [
                [
                    'property' => '05d9017b-f141-46fd-9266-9eca871e0e45',
                ],
                'property',
                'error'
            ],
        ];
    }

    public function uuidFailDataProvider(): array
    {
        return [
            [
                [],
                'property',
                'error #1'
            ],
            [
                [
                    'property' => null,
                ],
                'property',
                'error #2'
            ],
            [
                [
                    'property' => 123,
                ],
                'property',
                'error #3'
            ],
            [
                [
                    'property' => '05d9017b-f141-46fd-9266-9eca871e0e',
                ],
                'property',
                'error #4'
            ],
        ];
    }

    public function dateSuccessDataProvider(): array
    {
        return [
            [
                [
                    'property' => '2020-12-25 20:00:00',
                ],
                'property',
                'error'
            ],
        ];
    }

    public function dateFailDataProvider(): array
    {
        return [
            [
                [],
                'property',
                'error #1'
            ],
            [
                [
                    'property' => null,
                ],
                'property',
                'error #2'
            ],
            [
                [
                    'property' => 123,
                ],
                'property',
                'error #3'
            ],
            [
                [
                    'property' => '2020-55-55 20:00:00',
                ],
                'property',
                'error #4'
            ],
        ];
    }
}
