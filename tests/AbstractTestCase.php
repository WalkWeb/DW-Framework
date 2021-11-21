<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{
    public function setUp(): void
    {
        if (file_exists(__DIR__ . '/../config.local.php')) {
            require_once __DIR__ . '/../config.local.php';
        } else {
            require_once __DIR__ . '/../config.php';
        }
    }
}
