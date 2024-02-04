<?php

declare(strict_types=1);

namespace Tests;

use NW\App;
use NW\AppException;
use NW\Connection;
use NW\Container;
use NW\Route\Router;
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
     * @param Router $router
     * @return App
     * @throws AppException
     */
    protected function getApp(Router $router): App
    {
        return new App($router, $this->getContainer());
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
            CACHE_DIR,
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
     * @param string $table
     * @throws AppException
     */
    protected function clearTable(Connection $db, string $table): void
    {
        $db->query("DELETE FROM `$table`;");
    }
}
