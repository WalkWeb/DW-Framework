<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Connection;
use Exception;
use ReflectionException;
use ReflectionProperty;
use Tests\AbstractTestCase;

/**
 * ВАЖНО: Выполнение этих тестов требует настройку подключения к существующей базе в config.test.php или config.php
 *
 * Для тестов достаточно просто пустой базы, никаких таблиц и данных в ней не требуется
 *
 * Если работа с MySQL базой не планируется (а соответственно и тестировать данный функционал не нужно) - просто удалите
 * этот класс
 *
 * @package Tests\src\NWFramework
 */
class ConnectionTest extends AbstractTestCase
{
    /**
     * Тест на успешное подключение к MySQL базе
     *
     * @throws Exception
     */
    public function testConnectionCreateSuccess(): void
    {
        $db = Connection::getInstance();

        // Если при подключении не произошло исключений - значит оно прошло успешно
        self::assertTrue($db->success());
        self::assertEquals('', $db->getError());
    }

    /**
     * Тест на ошибку подключения к MySQL базе
     *
     * @throws Exception
     */
    public function testConnectionCreateFail(): void
    {
        $this->cleanConnect();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Connection::ERROR_CONNECT . "mysqli_connect(): (HY000/1045): Access denied for user 'user'@'localhost' (using password: YES)");
        Connection::getInstance('localhost', 'user', 'invalid_pass', 'db');
    }

    /**
     * @throws Exception
     */
    public function testConnectionQuerySuccess(): void
    {
        $user = 'User#1';
        $db = Connection::getInstance();
        $db->autocommit(false);

        // Create table
        $this->createTable($db);

        // Insert
        $db->query(
            "INSERT INTO `users` (`name`) VALUES (?);",
            [['type' => 's', 'value' => $user]]
        );

        self::assertIsInt($db->insertId());

        // Select all
        $users = $db->query('SELECT `name` FROM `users`');

        self::assertCount(1, $users);

        foreach ($users as $userData) {
            self::assertEquals($user, $userData['name']);
        }

        // Select one
        // TODO На этом этапе почему-то получаем пустой массив. А без rollback все работает. Надо разобраться почему
//        $id = 1;
//        $userData = $db->query(
//            'SELECT `name` FROM `users` WHERE id = ?',
//            [['type' => 'i', 'value' => $id]],
//            true
//        );
//
//        self::assertEquals($user, $userData['name']);

        self::assertEquals(3, $db->getQueryNumber());

        $db->rollback();
    }

    /**
     * Обнуляет синглтон, чтобы он пересоздался
     *
     * @throws Exception
     * @throws ReflectionException
     */
    private function cleanConnect(): void
    {
        $instance = Connection::getInstance();
        $reflection = new ReflectionProperty(get_class($instance), 'instance');
        $reflection->setAccessible(true);
        $reflection->setValue($instance, null);
    }

    /**
     * @param Connection $db
     * @throws Exception
     */
    private function createTable(Connection $db): void
    {
        $db->query('CREATE TABLE IF NOT EXISTS `users` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL 
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    }
}
