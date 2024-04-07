<?php

declare(strict_types=1);

namespace NW;

use DateTime;
use Exception;
use NW\MySQL\Connection;
use NW\Route\RouteCollection;
use NW\Route\Router;

class Migration
{
    public const FILE_PREFIX    = 'Version';
    public const TABLE_NAME     = 'migrations';
    public const MIGRATIONS_DIR = __DIR__ . '/../../migrations/';

    private Connection $connection;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $router = new Router(new RouteCollection());
        $this->connection = (new App($router, Container::create()))->getContainer()->getConnectionPool()->getConnection();
    }

    /**
     * @return string - Created filepath
     */
    public function create(): string
    {
        $date = new DateTime();
        $microtime = microtime(true);
        $millisecond = round($microtime - floor($microtime), 2) * 100;

        $className = "Version_{$date->format('Y_m_d_H_i_s')}_$millisecond";

        $fileContent = '<?php

declare(strict_types=1);

namespace Migrations;

use NW\MySQL\Connection;

class ' . $className . '
{
    public function run(Connection $connection): void
    {
        //$connection->query(\'...Your SQL code...\');
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
            $migration->run($this->connection);
            $this->connection->query("INSERT INTO " . self::TABLE_NAME . " (version) VALUES ('$migrationForRun')");
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
        $this->connection->query(
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
        return $this->connection->query("SELECT `version` FROM " . self::TABLE_NAME);
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
