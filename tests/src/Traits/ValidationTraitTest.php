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
}
