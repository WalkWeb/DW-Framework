<?php

declare(strict_types=1);

namespace Tests\src\MySQL;

use WalkWeb\NW\AppException;
use WalkWeb\NW\MySQL\Connection;
use WalkWeb\NW\Container;
use Tests\AbstractTest;

/**
 * ВАЖНО: Выполнение этих тестов требует настройку подключения к существующей базе в config.test.php или config.php
 *
 * Если работа с MySQL базой не планируется (а соответственно и тестировать данный функционал не нужно) - просто удалите
 * этот класс
 *
 * @package Tests\src
 */
class ConnectionTest extends AbstractTest
{
    /**
     * Тест на успешное подключение к MySQL базе
     *
     * @throws AppException
     */
    public function testConnectionCreateSuccess(): void
    {
        $db = $this->getContainer()->getConnectionPool()->getConnection();

        // Если при подключении не произошло исключений - значит оно прошло успешно
        self::assertTrue($db->isSuccess());
        self::assertEquals('', $db->getError());
    }

    /**
     * @throws AppException
     */
    public function testConnectionQueryLogs(): void
    {
        $db = $this->getContainer()->getConnectionPool()->getConnection();

        self::assertEquals(0, $db->getCountQuery());
        self::assertEquals([], $db->getQueries());

        $sql1 = "SELECT 'hello'";
        $sql2 = "SELECT 'my'";
        $sql3 = "SELECT 'world'";

        $db->query($sql1);
        $db->query($sql2);
        $db->query($sql3);

        self::assertEquals(3, $db->getCountQuery());
        self::assertEquals([$sql1, $sql2, $sql3], $db->getQueries());
    }

    /**
     * Тест на ошибку подключения к MySQL базе
     *
     * @throws AppException
     */
    public function testConnectionCreateFailException(): void
    {
        $this->expectException(AppException::class);
        // Проверяем лишь по основной части сообщения: Невозможно подключиться к MySQL: mysqli_connect()
        // В разных вариантах запуска базы полный текст ошибки будет разным, например:
        // MySQL установленный локально: Невозможно подключиться к MySQL: mysqli_connect(): (HY000/1045): Access denied for user 'user'@'localhost' (using password: YES)
        // MariaDB установленная через докер: Невозможно подключиться к MySQL: mysqli_connect(): (HY000/2002): No such file or directory
        $this->expectExceptionMessage(Connection::ERROR_CONNECT . "mysqli_connect()");
        new Connection('localhost', 'user', 'invalid_pass', 'db', $this->getContainer());
    }

    /**
     * Тест на проверку записи в логах об ошибке
     *
     * @throws AppException
     */
    public function testConnectionCreateFailLog(): void
    {
        $container = $this->getContainer();
        try {
            new Connection('localhost', 'user', 'invalid_pass', 'db', $container);
        } catch (AppException $e) {
            // Ignore exception
        }

        self::assertEquals(
            ["Невозможно подключиться к MySQL: mysqli_connect(): (HY000/1045): Access denied for user 'user'@'localhost' (using password: YES)"],
            $container->getLogger()->getLogs()
        );
    }

    /**
     * @throws AppException
     */
    public function testConnectionQuerySuccess(): void
    {
        $id = 'bc313e0f-ba0f-4e04-b17e-f796d7ba8be0';
        $user = 'Book#1';
        $db = $this->getContainer()->getConnectionPool()->getConnection();
        $db->autocommit(false);

        // Insert
        $db->query(
            "INSERT INTO `books` (`id`, `name`) VALUES (?, ?);",
            [
                ['type' => 's', 'value' => $id],
                ['type' => 's', 'value' => $user],
            ]
        );

        self::assertIsInt($id = $db->getInsertId());

        // Select all
        $users = $db->query("SELECT `name` FROM `books`");

        self::assertCount(1, $users);
        self::assertEquals($user, $users[0]['name']);

        // Select one
        $userData = $db->query(
            'SELECT `name` FROM `books` WHERE id = ?',
            [['type' => 'i', 'value' => $id]],
            true
        );

        self::assertEquals($user, $userData['name']);

        self::assertEquals(3, $db->getCountQuery());

        $db->rollback();
    }

    /**
     * Пытаемся сделать SELECT к неизвестной таблице
     *
     * @throws AppException
     */
    public function testConnectionQueryError(): void
    {
        $container = Container::create();
        $db = $container->getConnectionPool()->getConnection();
        $sql = 'SELECT * FROM unknown_table';
        $error = "Ошибка выполнения SQL: 1146 Table 'dw-framework.unknown_table' doesn't exist. SQL: SELECT * FROM unknown_table";

        try {
            $db->query($sql);
        } catch (AppException $e) {
            self::assertEquals($db->getError(), $e->getMessage());
        }

        self::assertEquals($error, $db->getError());
        self::assertEquals([$error], $container->getLogger()->getLogs());
    }
}
