<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Logs;
use ReflectionProperty;
use Tests\AbstractTestCase;

class LogsTest extends AbstractTestCase
{
    public function testLogsSetGetLog(): void
    {
        $this->cleanLogs();

        $log = 'test log';

        Logs::setLogs($log);

        self::assertEquals('<p class="logs">&bull; ' . $log . '<br />', Logs::getLogs());
    }

    public function testLogsGetJsonLogs(): void
    {
        $this->cleanLogs();

        $log = 'test log';

        Logs::resetLog();
        Logs::setLogs($log);

        self::assertEquals(
            str_replace('"', '\\"', '<p class="logs">&bull; ' . $log . '<br />'),
            Logs::getJsonLogs()
        );
    }

    /**
     * Обнуляет логи, так как они статические и используются напрямую в других классах, например в Connection
     */
    private function cleanLogs(): void
    {
        $reflection = new ReflectionProperty(Logs::class, 'logs');
        $reflection->setAccessible(true);
        $reflection->setValue(Logs::class, '<p class="logs">');
    }
}
