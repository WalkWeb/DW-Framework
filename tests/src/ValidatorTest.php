<?php

declare(strict_types=1);

namespace Tests\src;

use Exception;
use WalkWeb\NW\AppException;
use Tests\AbstractTest;

class ValidatorTest extends AbstractTest
{
    /**
     * @throws AppException
     */
    public function testValidatorEmail(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 'mail@mail.com', ['mail']));

        // False
        self::assertFalse($validator->check('param', 'mail@mail', ['mail']));
        self::assertEquals('Invalid email', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorInteger(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 100, ['int']));

        // False
        self::assertFalse($validator->check('param', '100', ['int']));
        self::assertEquals('param expected int', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorString(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 'name', ['string']));

        // False
        self::assertFalse($validator->check('param', 100, ['string']));
        self::assertEquals('param expected string', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorBoolean(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', false, ['boolean']));

        // False
        self::assertFalse($validator->check('param', 1, ['boolean']));
        self::assertEquals('param expected boolean', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorIn(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 10, ['in' => [10, 20, 30]]));

        // False
        self::assertFalse($validator->check('param', 100, ['in' => [10, 20, 30]]));
        self::assertEquals('param invalid value', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorParent(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 'Login', ['parent' => '/^[a-zA-Z0-9а-яА-ЯёЁ\-_]*$/u',]));

        // False
        self::assertFalse($validator->check('param', 'InvalidLogin&', ['parent' => '/^[a-zA-Z0-9а-яА-ЯёЁ\-_]*$/u',]));
        self::assertEquals('param does not match the pattern', $validator->getError());
    }

    /**
     * Проверка минимальной длины строки
     *
     * @throws AppException
     */
    public function testValidatorMinString(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 'Login', ['string', 'min' => 5]));

        // False
        self::assertFalse($validator->check('param', 'Login', ['string', 'min' => 10]));
        self::assertEquals('param expected length >= 10', $validator->getError());
    }

    /**
     * Проверка минимального значения int
     *
     * @throws AppException
     */
    public function testValidatorMinInt(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 10, ['int', 'min' => 9]));

        // False
        self::assertFalse($validator->check('param', 9, ['int', 'min' => 10]));
        self::assertEquals('param expected >= 10', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorMaxString(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 'Login', ['string', 'max' => 10]));

        // False
        self::assertFalse($validator->check('param', 'Login', ['string', 'max' => 3]));
        self::assertEquals('param expected length <= 3', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorMaxInt(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 10, ['int', 'max' => 10]));

        // False
        self::assertFalse($validator->check('param', 4, ['int', 'max' => 3]));
        self::assertEquals('param expected <= 3', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorRequired(): void
    {
        $validator = $this->getContainer()->getValidator();

        // Success
        self::assertTrue($validator->check('param', 123, ['required']));

        // False
        self::assertFalse($validator->check('param', null, ['required']));
        self::assertEquals('param cannot be empty', $validator->getError());

        // No required
        self::assertTrue($validator->check('no required', null, []));
    }

    /**
     * @throws AppException
     */
    public function testValidatorCustomError(): void
    {
        $validator = $this->getContainer()->getValidator();

        $error = 'Вы указали некорректный логи';
        $rules = [
            'required',
            'string',
            'min'    => 5,
            'max'    => 15,
            'parent' => '/^[a-zA-Z0-9а-яА-ЯёЁ\-_]*$/u',
            'unique',
        ];

        self::assertFalse($validator->check('param', 'InvalidLogin&', $rules, null, null, $error));
        self::assertEquals($error, $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorUniqueSuccess(): void
    {
        $validator = $this->getContainer()->getValidator();

        $rules = [
            'required',
            'string',
            'unique',
        ];

        self::assertTrue($validator->check('param', 'abc', $rules, 'default/books', 'name'));
        self::assertEquals('', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorUniqueNoDatabaseAndTable(): void
    {
        $validator = $this->getContainer()->getValidator();

        $rules = [
            'required',
            'string',
            'unique',
        ];

        self::assertFalse($validator->check('param', 'abc', $rules, null, 'name'));
        self::assertEquals('Missed database/table', $validator->getError());
    }

    /**
     * Тест на ситуацию, когда данные по базе/таблицы указаны некорректно
     *
     * @throws AppException
     */
    public function testValidatorUniqueInvalidDatabaseAndTable(): void
    {
        $validator = $this->getContainer()->getValidator();

        $rules = [
            'required',
            'string',
            'unique',
        ];

        self::assertFalse($validator->check('param', 'abc', $rules, 'invalid_data', 'name'));
        self::assertEquals('Invalid database or table info, expected "database/name"', $validator->getError());
    }

    /**
     * @throws AppException
     */
    public function testValidatorUniqueNoColumn(): void
    {
        $validator = $this->getContainer()->getValidator();

        $rules = [
            'required',
            'string',
            'unique',
        ];

        self::assertFalse($validator->check('param', 'abc', $rules, 'users'));
        self::assertEquals('Missed database/table', $validator->getError());
    }

    /**
     * @throws Exception
     */
    public function testValidatorUniqueExist(): void
    {
        $container = $this->getContainer();
        $connection = $container->getConnectionPool()->getConnection();
        $id = '6e9043d1-18fb-44ea-be60-c356048f63a2';
        $book = 'Book-1';
        $validator = $container->getValidator();

        $connection->autocommit(false);

        $rules = [
            'required',
            'string',
            'unique',
        ];

        // Clear table
        $this->clearTable($connection, 'books');

        // Insert data
        $this->insert($connection, $id, $book);

        self::assertFalse($validator->check('param', $book, $rules, 'default/books', 'name'));
        self::assertEquals('specified value in param already exists, specify another one', $validator->getError());

        $connection->rollback();
    }
}
