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
     * @dataProvider stringSuccessDataProvider
     * @param array $data
     * @param string $field
     * @throws AppException
     */
    public function testValidationStringSuccess(array $data, string $field): void
    {
        self::assertEquals($data[$field], self::string($data, $field, ''));
    }

    /**
     * @dataProvider stringFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationStringFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::string($data, $field, $error);
    }

    /**
     * @dataProvider stringMinMaxLengthSuccessDataProvider
     * @param string $string
     * @param int $min
     * @param int $max
     * @throws AppException
     */
    public function testValidationStringMinMaxLengthSuccess(string $string, int $min, int $max): void
    {
        self::assertEquals($string, self::stringMinMaxLength($string, $min, $max, ''));
    }

    /**
     * @dataProvider stringMinMaxLengthFailDataProvider
     * @param string $string
     * @param int $min
     * @param int $max
     * @param string $error
     */
    public function testValidationStringMinMaxLengthFail(string $string, int $min, int $max, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::stringMinMaxLength($string, $min, $max, $error);
    }

    /**
     * @dataProvider intSuccessDataProvider
     * @param array $data
     * @param string $field
     * @throws AppException
     */
    public function testValidationIntSuccess(array $data, string $field): void
    {
        self::assertEquals($data[$field], self::int($data, $field, ''));
    }

    /**
     * @dataProvider intFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationIntFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::int($data, $field, $error);
    }

    /**
     * @throws AppException
     */
    public function testValidationParentSuccess(): void
    {
        $parent = '/[a-zA-Z0-9]+/';
        $string = 'АБСabc01239';

        self::assertEquals($string, self::parent($string, $parent, ''));
    }

    /**
     * @throws AppException
     */
    public function testValidationParentFail(): void
    {
        $parent = '/[a-zA-Z0-9]+/';
        $string = '----';
        $error = 'parent error';

        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::parent($string, $parent, $error);
    }

    /**
     * @dataProvider emailSuccessDataProvider
     * @param string $email
     * @throws AppException
     */
    public function testValidationEmailSuccess(string $email): void
    {
        self::assertEquals($email, self::email($email, ''));
    }

    /**
     * @dataProvider emailFailDataProvider
     * @param string $email
     * @param string $error
     */
    public function testValidationEmailFail(string $email, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::email($email, $error);
    }

    /**
     * @throws AppException
     */
    public function testValidationBoolSuccess(): void
    {
        self::assertTrue(self::bool(['field' => true], 'field', ''));
        self::assertFalse(self::bool(['field' => false], 'field', ''));
    }

    /**
     * @dataProvider boolFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationBoolFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::bool($data, $field, $error);
    }

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
     * @param string $field
     * @throws AppException
     */
    public function testValidationIntOrFloatSuccess(array $data, string $field): void
    {
        self::assertEquals($data[$field], self::intOrFloat($data, $field, 'error'));
    }

    /**
     * @dataProvider intOrFloatFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationIntOrFloatFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::intOrFloat($data, $field, $error);
    }

    /**
     * @dataProvider arraySuccessDataProvider
     * @param array $data
     * @param string $field
     * @throws AppException
     */
    public function testValidationArraySuccess(array $data, string $field): void
    {
        self::assertEquals($data[$field], self::array($data, $field, 'error'));
    }

    /**
     * @dataProvider arrayFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationArrayFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::array($data, $field, $error);
    }

    /**
     * @dataProvider stringOrNullSuccessDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @param string|null $expected
     * @throws AppException
     */
    public function testValidationStringOrNullSuccess(array $data, string $field, string $error, ?string $expected): void
    {
        self::assertEquals($expected, self::stringOrNull($data, $field, $error));
    }

    /**
     * @dataProvider stringOrNullFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationStringOrNullFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::stringOrNull($data, $field, $error);
    }

    /**
     * @dataProvider stringOrDefaultDataProvider
     * @param array $data
     * @param string $field
     * @param string $default
     * @param string $expected
     */
    public function testValidationStringOrDefault(array $data, string $field, string $default, string $expected): void
    {
        self::assertEquals($expected, self::stringOrDefault($data, $field, $default));
    }

    /**
     * @dataProvider uuidSuccessDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationUuidSuccess(array $data, string $field, string $error): void
    {
        self::assertEquals($data[$field], self::uuid($data, $field, $error));
    }

    /**
     * @dataProvider uuidFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationUuidFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::uuid($data, $field, $error);
    }

    /**
     * @dataProvider dateSuccessDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws Exception
     */
    public function testValidationDateSuccess(array $data, string $field, string $error): void
    {
        self::assertEquals(new DateTime($data[$field]), self::date($data, $field, $error));
    }

    /**
     * @dataProvider dateFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws Exception
     */
    public function testValidationDateFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::date($data, $field, $error);
    }

    /**
     * @dataProvider dateOrNullSuccessDataProvider
     * @param array $data
     * @param string $field
     * @param string|null $expected
     * @throws Exception
     */
    public function testValidationDateOrNullSuccess(array $data, string $field, ?string $expected = null): void
    {
        if ($expected === null) {
            self::assertNull(self::dateOrNull($data, $field, ''));
        } else {
            self::assertEquals(new DateTime($expected), self::dateOrNull($data, $field, ''));
        }
    }

    /**
     * @dataProvider dateFailDateOrNullProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws Exception
     */
    public function testValidationDateOrNullFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::dateOrNull($data, $field, $error);
    }

    /**
     * @dataProvider uuidOrNullSuccessDataProvider
     * @param array $data
     * @param string $field
     * @param string|null $expected
     * @throws AppException
     */
    public function testValidationUuidOrNullSuccess(array $data, string $field, ?string $expected): void
    {
        if ($expected === null) {
            self::assertNull(self::uuidOrNull($data, $field, ''));
        } else {
            self::assertEquals($expected, self::uuidOrNull($data, $field, ''));
        }
    }

    /**
     * @dataProvider uuidOrNullFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     * @throws AppException
     */
    public function testValidationUuidOrNullFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::uuidOrNull($data, $field, $error);
    }

    /**
     * @dataProvider intOrNullSuccessDataProvider
     * @param array $data
     * @param string $field
     * @param int|null $expected
     * @throws AppException
     */
    public function testValidationIntOrNullSuccess(array $data, string $field, ?int $expected): void
    {
        if ($expected === null) {
            self::assertNull(self::intOrNull($data, $field, ''));
        } else {
            self::assertEquals($expected, self::intOrNull($data, $field, ''));
        }
    }

    /**
     * @dataProvider intOrNullFailDataProvider
     * @param array $data
     * @param string $field
     * @param string $error
     */
    public function testValidationIntOrNullFail(array $data, string $field, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        self::intOrNull($data, $field, $error);
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

    /**
     * @return array
     * @throws AppException
     */
    public function stringSuccessDataProvider(): array
    {
        return [
            [
                ['field' => ''],
                'field',
            ],
            [
                ['field' => 'abc'],
                'field',
            ],
            [
                ['field' => self::generateString(100)],
                'field',
            ],
        ];
    }

    public function stringFailDataProvider(): array
    {
        return [
            [
                ['field' => 1],
                'field',
                'error-1',
            ],
            [
                ['field' => null],
                'field',
                'error-2',
            ],
            [
                ['field' => true],
                'field',
                'error-3',
            ],
        ];
    }

    /**
     * @return array
     */
    public function stringMinMaxLengthSuccessDataProvider(): array
    {
        return [
            [
                'abc',
                1,
                5,
            ],
            [
                'abc',
                3,
                3,
            ],
            [
                '',
                0,
                0,
            ],
        ];
    }

    /**
     * @return array
     */
    public function stringMinMaxLengthFailDataProvider(): array
    {
        return [
            [
                'abc',
                4,
                5,
                'error-1',
            ],
            [
                'abc',
                1,
                2,
                'error-2',
            ],
            [
                'abc_abc',
                3,
                4,
                'error-3',
            ],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function intSuccessDataProvider(): array
    {
        return [
            [
                ['field' => 0],
                'field',
            ],
            [
                ['field' => 100],
                'field',
            ],
            [
                ['field' => random_int(0, 1000000)],
                'field',
            ],
        ];
    }

    /**
     * @return array
     */
    public function intFailDataProvider(): array
    {
        return [
            [
                ['field' => 1.0],
                'field',
                'error-1',
            ],
            [
                ['field' => null],
                'field',
                'error-2',
            ],
            [
                ['field' => '1'],
                'field',
                'error-3',
            ],
        ];
    }

    /**
     * @return array
     */
    public function emailSuccessDataProvider(): array
    {
        return [
            [
                'mail@mail.com',
            ],
            [
                'a@a.a',
            ],
            [
                'a-b-c@a-b-c.com',
            ],
        ];
    }

    /**
     * @return array
     */
    public function emailFailDataProvider(): array
    {
        return [
            [
                'abc',
                'error-1',
            ],
            [
                '----@----.a',
                'error-2',
            ],
            [
                '$%#@&$#.com',
                'error-3',
            ],
        ];
    }

    /**
     * @return array
     */
    public function boolFailDataProvider(): array
    {
        return [
            [
                ['field' => 1],
                'field',
                'error-1',
            ],
            [
                ['field' => 'true'],
                'field',
                'error-2',
            ],
            [
                ['field' => null],
                'field',
                'error-3',
            ],
        ];
    }

    /**
     * @return array
     */
    public function dateOrNullSuccessDataProvider(): array
    {
        return [
            [
                [
                    'field' => '2020-12-25 20:00:00',
                ],
                'field',
                '2020-12-25 20:00:00'
            ],
            [
                [
                    'field' => null,
                ],
                'field',
                null,
            ],
        ];
    }

    /**
     * @return array
     */
    public function dateFailDateOrNullProvider(): array
    {
        return [
            // miss field
            [
                [],
                'field',
                'error-1',
            ],
            // field invalid type
            [
                [
                    'field' => 123,
                ],
                'field',
                'error-2',
            ],
            // field invalid date
            [
                [
                    'field' => '2020-99-99 20:00:00',
                ],
                'field',
                'error-3',
            ],
        ];
    }

    /**
     * @return array
     */
    public function uuidOrNullSuccessDataProvider(): array
    {
        return [
            [
                [
                    'field' => '4445e810-ab25-481b-a798-81062fa40b76',
                ],
                'field',
                '4445e810-ab25-481b-a798-81062fa40b76'
            ],
            [
                [
                    'field' => null,
                ],
                'field',
                null,
            ],
        ];
    }

    /**
     * @return array
     */
    public function uuidOrNullFailDataProvider(): array
    {
        return [
            // miss field
            [
                [],
                'field',
                'error-1',
            ],
            // field invalid type
            [
                [
                    'field' => 123,
                ],
                'field',
                'error-2',
            ],
            // field invalid uuid
            [
                [
                    'field' => '7c28beea-0b3e-425f-8121-10dfab8db861xxx',
                ],
                'field',
                'error-3',
            ],
        ];
    }

    /**
     * @return array
     */
    public function intOrNullSuccessDataProvider(): array
    {
        return [
            [
                [
                    'field' => 123,
                ],
                'field',
                123
            ],
            [
                [
                    'field' => null,
                ],
                'field',
                null,
            ],
        ];
    }

    /**
     * @return array
     */
    public function intOrNullFailDataProvider(): array
    {
        return [
            // miss field
            [
                [],
                'field',
                'error-1',
            ],
            // field invalid type
            [
                [
                    'field' => 'string',
                ],
                'field',
                'error-2',
            ],
        ];
    }
}
