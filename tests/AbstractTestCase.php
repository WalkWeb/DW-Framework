<?php

declare(strict_types=1);

namespace Tests;

use NW\App;
use NW\AppException;
use NW\Connection;
use NW\Container;
use NW\Route\Router;
use NW\Runtime;
use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{
    protected App $app;
    protected string $dir;

    /**
     * @throws AppException
     */
    public function setUp(): void
    {
        $this->dir = __DIR__;

        if (file_exists(__DIR__ . '/../config.test.php')) {
            require_once __DIR__ . '/../config.test.php';
        } else {
            require_once __DIR__ . '/../config.php';
        }

        $router = require __DIR__ . '/../routes/web.php';
        $this->app = new App($router, $this->getContainer());
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
     * @param string $viewDir
     * @return Container
     * @throws AppException
     */
    protected function getContainer(string $appEnv = APP_ENV, string $viewDir = VIEW_DIR): Container
    {
        $container = new Container(
            $appEnv,
            DB_HOST,
            DB_USER,
            DB_PASSWORD,
            DB_NAME,
            SAVE_LOG,
            LOG_DIR,
            LOG_FILE_NAME,
            HANDLERS_DIR,
            CACHE_DIR,
            $viewDir,
        );
        $container->set(Runtime::class, new Runtime());

        return $container;
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
