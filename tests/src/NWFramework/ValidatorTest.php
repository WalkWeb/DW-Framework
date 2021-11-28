<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Exception;
use NW\Validator;
use Tests\AbstractTestCase;

class ValidatorTest extends AbstractTestCase
{
    /**
     * @throws Exception
     */
    public function testValidatorEmail(): void
    {
        // Success
        self::assertTrue(Validator::check('email', 'mail@mail.com', ['mail']));

        // False
        self::assertFalse(Validator::check('email', 'mail@mail', ['mail']));
        self::assertEquals('Указана некорректная почта', Validator::getError());
    }

    /**
     * @throws Exception
     */
    public function testValidatorInteger(): void
    {
        // Success
        self::assertTrue(Validator::check('integer', 100, ['int']));

        // False
        self::assertFalse(Validator::check('integer', '100', ['int']));
        self::assertEquals('integer должен быть числом', Validator::getError());
    }

    /**
     * @throws Exception
     */
    public function testValidatorString(): void
    {
        // Success
        self::assertTrue(Validator::check('string', 'name', ['string']));

        // False
        self::assertFalse(Validator::check('string', 100, ['string']));
        self::assertEquals('string должен быть строкой', Validator::getError());
    }

    /**
     * @throws Exception
     */
    public function testValidatorBoolean(): void
    {
        // Success
        self::assertTrue(Validator::check('boolean', false, ['boolean']));

        // False
        self::assertFalse(Validator::check('boolean', 1, ['boolean']));
        self::assertEquals('boolean должен быть логическим типом', Validator::getError());
    }

    /**
     * @throws Exception
     */
    public function testValidatorIn(): void
    {
        // Success
        self::assertTrue(Validator::check('in', 10, ['in' => [10, 20, 30]]));

        // False
        self::assertFalse(Validator::check('in', 100, ['in' => [10, 20, 30]]));
        self::assertEquals('in указан некорректно', Validator::getError());
    }

    /**
     * @throws Exception
     */
    public function testValidatorParent(): void
    {
        // Success
        self::assertTrue(Validator::check('parent', 'Login', ['parent' => '/^[a-zA-Z0-9а-яА-ЯёЁ\-_]*$/u',]));

        // False
        self::assertFalse(Validator::check('parent', 'InvalidLogin&', ['parent' => '/^[a-zA-Z0-9а-яА-ЯёЁ\-_]*$/u',]));
        self::assertEquals('parent указан некорректно', Validator::getError());
    }

    /**
     * На данный момент реализована только проверка минимальной/максимальной длины строки
     *
     * @throws Exception
     */
    public function testValidatorMin(): void
    {
        // Success
        self::assertTrue(Validator::check('min string', 'Login', ['string', 'min' => 5]));

        // False
        self::assertFalse(Validator::check('min string', 'Login', ['string', 'min' => 10]));
        self::assertEquals('min string должен быть больше или равен 10 символов', Validator::getError());

    }

    /**
     * @throws Exception
     */
    public function testValidatorMax(): void
    {
        // Success
        self::assertTrue(Validator::check('max string', 'Login', ['string', 'max' => 10]));

        // False
        self::assertFalse(Validator::check('max string', 'Login', ['string', 'max' => 3]));
        self::assertEquals('max string должен быть меньше или равен 3 символов', Validator::getError());
    }

    /**
     * @throws Exception
     */
    public function testValidatorRequired(): void
    {
        // Success
        self::assertTrue(Validator::check('required', 123, ['required']));

        // False
        self::assertFalse(Validator::check('required', null, ['required']));
        self::assertEquals('required не может быть пустым', Validator::getError());

        // No required
        self::assertTrue(Validator::check('no required', null, []));
    }

    /**
     * @throws Exception
     */
    public function testValidatorCustomError(): void
    {
        $error = 'Вы указали некорректный логи';
        $rules = [
            'required',
            'string',
            'min' => 5,
            'max' => 15,
            'parent' => '/^[a-zA-Z0-9а-яА-ЯёЁ\-_]*$/u',
            'unique',
        ];

        self::assertFalse(Validator::check('login', 'InvalidLogin&', $rules, null, null, $error));
        self::assertEquals($error, Validator::getError());
    }
}
