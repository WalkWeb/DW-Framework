<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Exception;
use NW\Connection;
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
        // Вначале удаляем все старые файлы миграций
        $this->removeMigrations();

        // Создаем новую миграцию
        $this->getMigration()->create();

        // Получаем созданную миграцию
        $migrations = [];
        $files = scandir(self::MIGRATIONS_DIR);

        foreach ($files as $file) {
            if (strripos($file, Migration::FILE_PREFIX) === 0) {
                $migrations[] = $file;
            }
        }

        // Проверяем, что создана одна миграция
        self::assertCount(1, $migrations);

        // Получаем содержимое
        $content = file_get_contents(self::MIGRATIONS_DIR . $migrations[0]);

        // Так как содержимое миграции будет всегда уникальным, проверяем лишь наличие некоторого текста
        self::assertIsInt(stripos($content, 'Version'));
        self::assertIsInt(stripos($content, 'public function run(Connection $connection): void'));
        self::assertIsInt(stripos($content, '//$connection->query(\'...Your SQL code...\');'));

        // В завершение удаляем созданную миграцию
        $this->removeMigrations();
    }

    /**
     * Тест на выполнение миграции
     *
     * @return void
     * @throws Exception
     */
    public function testMigrationRun(): void
    {
        $connection = new Connection();
        $tableName = Migration::TABLE_NAME;

        // Вначале удаляем таблицу миграций и файлы миграций, если они есть
        $this->removeTable();
        $this->removeMigrations();

        // Создаем миграцию
        $this->getMigration()->create();

        // Выполняем миграцию
        $this->getMigration()->run();

        // Проверяем, что появилась таблица и запись с миграцией
        $table = $connection->query("SHOW TABLES LIKE '$tableName';");

        if (!$table) {
            self::fail("$tableName table not created");
        }

        // Проверяем наличие в ней одной записи
        $versions = $connection->query("SELECT * FROM $tableName");

        self::assertCount(1, $versions);

        // Удаляем созданную миграцию и таблицу
        $this->removeMigrations();
        $this->removeTable();
    }

    /**
     * @return Migration
     */
    private function getMigration(): Migration
    {
        return new Migration();
    }

    /**
     * @return void
     */
    private function removeMigrations(): void
    {
        $files = scandir(self::MIGRATIONS_DIR);

        foreach ($files as $file) {
            if (strripos($file, Migration::FILE_PREFIX) === 0) {
                unlink(self::MIGRATIONS_DIR . $file);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function removeTable(): void
    {
        $tableName = Migration::TABLE_NAME;
        (new Connection())->query("DROP TABLE IF EXISTS `$tableName`;");
    }
}
