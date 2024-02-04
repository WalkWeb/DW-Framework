<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Connection;
use Exception;
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
        $db = $this->getContainer()->getConnection();

        // Если при подключении не произошло исключений - значит оно прошло успешно
        self::assertTrue($db->isSuccess());
        self::assertEquals('', $db->getError());
    }

    /**
     * Тест на ошибку подключения к MySQL базе
     *
     * @throws Exception
     */
    public function testConnectionCreateFail(): void
    {
        $this->expectException(Exception::class);
        // Проверяем лишь по основной части сообщения: Невозможно подключиться к MySQL: mysqli_connect()
        // В разных вариантах запуска базы полный текст ошибки будет разным, например:
        // MySQL установленный локально: Невозможно подключиться к MySQL: mysqli_connect(): (HY000/1045): Access denied for user 'user'@'localhost' (using password: YES)
        // MariaDB установленная через докер: Невозможно подключиться к MySQL: mysqli_connect(): (HY000/2002): No such file or directory
        $this->expectExceptionMessage(Connection::ERROR_CONNECT . "mysqli_connect()");
        new Connection('localhost', 'user', 'invalid_pass', 'db', $this->getContainer());
    }

    /**
     * @throws Exception
     */
    public function testConnectionQuerySuccess(): void
    {
        $id = 'bc313e0f-ba0f-4e04-b17e-f796d7ba8be0';
        $user = 'User#1';
        $db = $this->getContainer()->getConnection();
        $db->autocommit(false);

        // Insert
        $db->query(
            "INSERT INTO `users` (`id`, `name`) VALUES (?, ?);",
            [
                ['type' => 's', 'value' => $id],
                ['type' => 's', 'value' => $user],
            ]
        );

        self::assertIsInt($id = $db->getInsertId());

        // Select all
        $users = $db->query("SELECT `name` FROM `users`");

        self::assertCount(1, $users);
        self::assertEquals($user, $users[0]['name']);

        // Select one
        $userData = $db->query(
            'SELECT `name` FROM `users` WHERE id = ?',
            [['type' => 'i', 'value' => $id]],
            true
        );

        self::assertEquals($user, $userData['name']);

        self::assertEquals(3, $db->getQueryNumber());

        $db->rollback();
    }
}
