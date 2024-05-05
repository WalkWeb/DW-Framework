<?php

declare(strict_types=1);

namespace Tests;

use WalkWeb\NW\App;
use WalkWeb\NW\AppException;
use WalkWeb\NW\MySQL\Connection;
use WalkWeb\NW\Container;
use WalkWeb\NW\Response;
use WalkWeb\NW\Route\Router;
use WalkWeb\NW\Runtime;
use WalkWeb\NW\Traits\StringTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    use StringTrait;

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
    protected function getContainer(
        string $appEnv = APP_ENV,
        string $viewDir = VIEW_DIR
    ): Container
    {
        $container = new Container(
            $appEnv,
            DB_CONFIGS,
            MAIL_CONFIG,
            SAVE_LOG,
            LOG_DIR,
            LOG_FILE_NAME,
            CACHE_DIR,
            $viewDir,
            TEMPLATE_DEFAULT,
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

    /**
     * Проверяет, что получен успешный json ответ
     *
     * @param Response $response
     */
    protected static function assertJsonSuccess(Response $response): void
    {
        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertEquals('{"success":true}', $response->getBody());
    }
}
