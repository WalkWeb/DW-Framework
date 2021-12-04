<?php

declare(strict_types=1);

namespace Tests;

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
}
