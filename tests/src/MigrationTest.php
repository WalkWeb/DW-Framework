<?php

declare(strict_types=1);

namespace Tests\src;

use Exception;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Migration;
use Tests\AbstractTest;

class MigrationTest extends AbstractTest
{
    private const ACTUAL_MIGRATION_COUNT = 3;

    private const MIGRATIONS_DIR = __DIR__ . '/../../migrations/';

    /**
     * Тест на создание миграции
     *
     * @return void
     * @throws AppException
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
        self::assertCount(self::ACTUAL_MIGRATION_COUNT, $migrations);

        // Получаем содержимое
        $content = file_get_contents(self::MIGRATIONS_DIR . $migrations[self::ACTUAL_MIGRATION_COUNT - 1]);

        // Так как содержимое миграции будет всегда уникальным, проверяем лишь наличие некоторого текста
        self::assertIsInt(stripos($content, 'Version'));
        self::assertIsInt(stripos($content, 'public function run(ConnectionPool $connectionPool): void'));
        self::assertIsInt(stripos($content, '//$connectionPool->getConnection()->query(\'...Your SQL code...\');'));

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
        $connection = $this->getContainer()->getConnectionPool()->getConnection();
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

        self::assertCount(self::ACTUAL_MIGRATION_COUNT, $versions);

        // Удаляем созданную миграцию и запись о её добавлении
        unlink($filePath);
        $this->removeInsertRow();
    }

    /**
     * Тест на ситуацию, когда указана неизвестная директория с миграциями
     *
     * @throws AppException
     */
    public function testMigrationDirMiss(): void
    {
        $migrationDir = 'invalid_migration_dir';
        $this->expectException(AppException::class);
        $this->expectExceptionMessage("Migration directory missed: $migrationDir");
        $migration = new Migration($this->getContainer(APP_ENV, VIEW_DIR, $migrationDir));
        $migration->create();
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
        $this->getContainer()->getConnectionPool()->getConnection()->query(
            "DELETE FROM `$tableName` ORDER BY executed_at DESC LIMIT 1;"
        );
    }
}
