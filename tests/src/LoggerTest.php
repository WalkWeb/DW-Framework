<?php

declare(strict_types=1);

namespace Tests\src;

use WalkWeb\NW\AppException;
use WalkWeb\NW\Logger;
use Tests\AbstractTest;

class LoggerTest extends AbstractTest
{
    /**
     * Тест на простое сохранение и получение логов
     *
     * @throws AppException
     */
    public function testLoggerSetAndGetLog(): void
    {
        $logger = new Logger(SAVE_LOG, LOG_DIR, LOG_FILE_NAME);

        $logger->addLog($log1 = 'test log #1');
        $logger->addLog($log2 = 'test log #2');

        self::assertEquals([$log1, $log2], $logger->getLogs());
    }

    /**
     * Тест на сохранение логов в файл
     *
     * @throws AppException
     */
    public function testLoggerSaveInFile(): void
    {
        $logger = new Logger(true, LOG_DIR, LOG_FILE_NAME);

        // Удаляем (возможно) уже существующие логи
        if (file_exists($logger->getLogsFilePath())) {
            unlink($logger->getLogsFilePath());
        }

        $logger->addLog($log = 'test log #1');

        self::assertEquals($log . "\n", file_get_contents($logger->getLogsFilePath()));
    }

    /**
     * Тест на ошибку сохранения логов в файл - когда указан некорректный путь до файла с логами
     *
     * Если быть точнее - некорректно указан путь к директории - сам файл создастся автоматически, если директория
     * доступна
     *
     * @throws AppException
     */
    public function testLoggerFileLogNotFound(): void
    {
        $logger = new Logger(true, $dir = 'invalid_dir', LOG_FILE_NAME);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Directory from save logs not found: ' . $dir);
        $logger->addLog($log = 'test log #1');
    }
}
