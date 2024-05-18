<?php

declare(strict_types=1);

namespace WalkWeb\NW;

use DateTime;
use Exception;
use WalkWeb\NW\MySQL\ConnectionPool;
use WalkWeb\NW\Route\RouteCollection;
use WalkWeb\NW\Route\Router;

class Migration
{
    public const FILE_PREFIX    = 'Version';
    public const TABLE_NAME     = 'migrations';

    // TODO Брать путь до миграций из контейнера
    public const MIGRATIONS_DIR = __DIR__ . '/../migrations/';

    private ConnectionPool $connectionPool;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $router = new Router(new RouteCollection());
        $this->connectionPool = (new App($router, Container::create()))->getContainer()->getConnectionPool();
    }

    /**
     * @return string - Created filepath
     */
    public function create(): string
    {
        $date = new DateTime();
        $microTime = microtime(true);
        $millisecond = round($microTime - floor($microTime), 2) * 100;

        $className = self::FILE_PREFIX . "_{$date->format('Y_m_d_H_i_s')}_$millisecond";

        $fileContent = '<?php

declare(strict_types=1);

namespace Migrations;

use WalkWeb\NW\AppException;
use WalkWeb\NW\MySQL\ConnectionPool;

class ' . $className . '
{
    /**
     * @param ConnectionPool $connectionPool
     * @throws AppException
     */
    public function run(ConnectionPool $connectionPool): void
    {
        //$connectionPool->getConnection()->query(\'...Your SQL code...\');
        echo "run ' . $className . '\n";
    }
}
';

        $filePath = self::MIGRATIONS_DIR . $className . '.php';
        $file = fopen($filePath, 'wb');
        fwrite($file, $fileContent);
        fclose($file);

        return $filePath;
    }

    /**
     * Выполняет новые миграции
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $this->createTable();
        $doneMigrations = $this->getDoneMigrations();
        $migrationsForRun = [];

        foreach ($this->getMigrations() as $file) {
            if (!$this->isDone($file, $doneMigrations)) {
                $migrationsForRun[] = $file;
            }
        }

        foreach ($migrationsForRun as $migrationForRun) {
            $className = 'Migrations\\' . pathinfo($migrationForRun, PATHINFO_FILENAME);
            $migration = new $className;
            $migration->run($this->connectionPool);
            $this->connectionPool->getConnection()->query("INSERT INTO " . self::TABLE_NAME . " (version) VALUES ('$migrationForRun')");
        }
    }

    /**
     * Проверяет наличие таблицы migrations, если нет - создает
     *
     * @return void
     * @throws Exception
     */
    private function createTable(): void
    {
        $this->connectionPool->getConnection()->query(
            "CREATE TABLE IF NOT EXISTS " . self::TABLE_NAME . " (
                    `version` VARCHAR(255) NOT NULL,
                    `executed_at` DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
    }

    /**
     * Получает список выполненных миграций из базы
     *
     * @return array
     * @throws Exception
     */
    private function getDoneMigrations(): array
    {
        return $this->connectionPool->getConnection()->query("SELECT `version` FROM " . self::TABLE_NAME);
    }

    /**
     * Получает список всех миграций (файлов). Файлы миграций должны начинаться с "Migration"
     *
     * @return array
     */
    private function getMigrations(): array
    {
        $migrations = [];
        $files = scandir(self::MIGRATIONS_DIR);

        foreach ($files as $file) {
            if (strripos($file, self::FILE_PREFIX) === 0) {
                $migrations[] = $file;
            }
        }

        return $migrations;
    }

    /**
     * Проверяет, были ли миграция уже выполнена
     *
     * @param string $file
     * @param array $runMigrations
     * @return bool
     */
    private function isDone(string $file, array $runMigrations): bool
    {
        foreach ($runMigrations as $runMigration) {
            if ($file === $runMigration['version']) {
                return true;
            }
        }

        return false;
    }
}
