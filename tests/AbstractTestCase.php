<?php

declare(strict_types=1);

namespace Tests;

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

    protected function getContainer(): Container
    {
        return new Container(
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
}
