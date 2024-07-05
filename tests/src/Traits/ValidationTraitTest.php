<?php

declare(strict_types=1);

namespace Tests\src\Traits;

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
}
