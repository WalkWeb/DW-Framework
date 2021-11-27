<?php

declare(strict_types=1);

namespace Tests\src;

use NW\Logs;
use Tests\AbstractTestCase;

class LogsTest extends AbstractTestCase
{
    public function testLogsSetGetLog(): void
    {
        $log = 'test log';

        Logs::setLogs($log);

        self::assertEquals('<p class="logs">&bull; ' . $log . '<br />', Logs::getLogs());
    }

    public function testLogsGetJsonLogs(): void
    {
        $log = 'test log';

        Logs::resetLog();
        Logs::setLogs($log);

        self::assertEquals(
            str_replace('"', '\\"', '<p class="logs">&bull; ' . $log . '<br />'),
            Logs::getJsonLogs()
        );
    }
}
