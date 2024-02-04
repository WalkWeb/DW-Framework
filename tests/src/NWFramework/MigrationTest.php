<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Exception;
use NW\Migration;
use Tests\AbstractTestCase;

class MigrationTest extends AbstractTestCase
{
    private const MIGRATIONS_DIR = __DIR__ . '/../../../migrations/';

    /**
     * Тест на создание миграции
     *
     * @return void
     */
    public function testMigrationCreate(): void
    {
        // Создаем новую миграцию
        $filePath = $this->getMigration()->create();

        // Получаем созданную миграцию
        $migrations = [];
        $files = scandir(self::MIGRATIONS_DIR);

        foreach ($files as $file) {
            if (strripos($file, Migration::FILE_PREFIX) === 0) {
                $migrations[] = $file;
            }
        }

        // Проверяем, что создана две миграции (одна базовая и одна созданная в тесте)
        self::assertCount(2, $migrations);

        // Получаем содержимое
        $content = file_get_contents(self::MIGRATIONS_DIR . $migrations[0]);

        // Так как содержимое миграции будет всегда уникальным, проверяем лишь наличие некоторого текста
        self::assertIsInt(stripos($content, 'Version'));
        self::assertIsInt(stripos($content, 'public function run(Connection $connection): void'));
        self::assertIsInt(stripos($content, '//$connection->query(\'...Your SQL code...\');'));

        // В завершение удаляем созданную миграцию
        unlink($filePath);
    }

    /**
     * Тест на выполнение миграции
     *
     * @return void
     * @throws Exception
     */
    public function testMigrationRun(): void
    {
        $connection = $this->getContainer()->getConnection();
        $tableName = Migration::TABLE_NAME;

        // Создаем миграцию
        $filePath = $this->getMigration()->create();

        // Выполняем миграцию
        $this->getMigration()->run();

        // Проверяем, что появилась таблица и запись с миграцией
        $table = $connection->query("SHOW TABLES LIKE '$tableName';");

        if (!$table) {
            self::fail("$tableName table not created");
        }

        // Проверяем наличие в ней двух записей (одна базовая и одна созданная в тесте)
        $versions = $connection->query("SELECT * FROM $tableName");

        self::assertCount(2, $versions);

        // Удаляем созданную миграцию и запись о её добавлении
        unlink($filePath);
        $this->removeInsertRow();
    }

    /**
     * @return Migration
     */
    private function getMigration(): Migration
    {
        return new Migration();
    }

    /**
     * @throws Exception
     */
    private function removeInsertRow(): void
    {
        $tableName = Migration::TABLE_NAME;
        $this->getContainer()->getConnection()->query(
            "DELETE FROM `$tableName` ORDER BY executed_at DESC LIMIT 1;"
        );
    }
}
