<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Log;
use ReflectionProperty;
use Tests\AbstractTestCase;

class LogTest extends AbstractTestCase
{
    public function testLogsSetGetLog(): void
    {
        $this->cleanLogs();

        $log = 'test log';

        Log::setLogs($log);

        self::assertEquals('<p class="logs">&bull; ' . $log . '<br />', Log::getLogs());
    }

    public function testLogsGetJsonLogs(): void
    {
        $this->cleanLogs();

        $log = 'test log';

        Log::resetLog();
        Log::setLogs($log);

        self::assertEquals(
            str_replace('"', '\\"', '<p class="logs">&bull; ' . $log . '<br />'),
            Log::getJsonLogs()
        );
    }

    /**
     * Обнуляет логи, так как они статические и используются напрямую в других классах, например в Connection
     */
    private function cleanLogs(): void
    {
        $reflection = new ReflectionProperty(Log::class, 'logs');
        $reflection->setAccessible(true);
        $reflection->setValue(Log::class, '<p class="logs">');
    }
}
