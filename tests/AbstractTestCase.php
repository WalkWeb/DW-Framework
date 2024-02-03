<?php

declare(strict_types=1);

namespace Tests;

use NW\AppException;
use NW\Connection;
use NW\Container;
use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{
    public function setUp(): void
    {
        if (file_exists(__DIR__ . '/../config.test.php')) {
            require_once __DIR__ . '/../config.test.php';
        } else {
            require_once __DIR__ . '/../config.php';
        }
    }

    /**
     * @param string $appEnv
     * @return Container
     * @throws AppException
     */
    protected function getContainer(string $appEnv = APP_ENV): Container
    {
        return new Container(
            $appEnv,
            DB_HOST,
            DB_USER,
            DB_PASSWORD,
            DB_NAME,
            SAVE_LOG,
            LOG_DIR,
            LOG_FILE_NAME,
            CONTROLLERS_DIR,
        );
    }

    /**
     * @param Connection $db
     * @param string $id
     * @param string $name
     * @throws AppException
     */
    protected function insert(Connection $db, string $id, string $name): void
    {
        $db->query(
            'INSERT INTO `books` (`id`, `name`) VALUES (?, ?);',
            [
                ['type' => 's', 'value' => $id],
                ['type' => 's', 'value' => $name],
            ]
        );
    }

    /**
     * @param Connection $db
     * @throws AppException
     */
    protected function createTable(Connection $db): void
    {
        $db->query('CREATE TABLE IF NOT EXISTS `books` (
            `id` VARCHAR(36) NOT NULL,
            `name` VARCHAR(255) NOT NULL 
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    }

    /**
     * @param Connection $db
     * @param string $table
     * @throws AppException
     */
    protected function clearTable(Connection $db, string $table): void
    {
        $db->query('DELETE FROM `books`;');
    }
}
