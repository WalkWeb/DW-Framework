<?php

namespace NW;

class Logger
{
    private const DEFAULT_LOG_DIRECTORY = DIR;
    private const DEFAULT_LOG_FILE_NAME = 'logs';

    /**
     * Место хранения файлов логов
     *
     * @var string
     */
    private string $dir;

    /**
     * Строка с логами
     *
     * @var string[]
     */
    private array $logs = [];

    /**
     * Сохранять ли логи
     *
     * @var bool
     */
    private bool $saveLog;

    /**
     * Сохранять ли логи в файл
     *
     * @var bool
     */
    private bool $saveFileLog;

    /**
     * Название файла с логами
     *
     * @var string
     */
    private string $logFileName;

    public function __construct(
        bool $saveLog = true,
        bool $saveFileLog = false,
        string $dir = self::DEFAULT_LOG_DIRECTORY,
        string $logFileName = self::DEFAULT_LOG_FILE_NAME
    )
    {
        $this->saveLog = $saveLog;
        $this->saveFileLog = $saveFileLog;
        $this->dir = $dir;
        $this->logFileName = $logFileName;
    }

    /**
     * Добавляет новый лог
     *
     * @param $log
     * @throws AppException
     */
    public function addLog($log): void
    {
        if ($this->saveLog) {
            $this->logs[] = $log;

            if ($this->saveFileLog) {
                $this->saveToFile($log);
            }
        }
    }

    /**
     * Возвращает логи
     *
     * @return array
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Возвращает путь к файлу с логами
     *
     * @return string
     * @throws AppException
     */
    public function getLogsFilePath(): string
    {
        if (!is_dir($this->dir)) {
            throw new AppException('Directory from save logs not found: ' . $this->dir);
        }

        return $this->dir . '/' . $this->logFileName;
    }

    /**
     * Сохраняет лог в файл
     *
     * @param $log
     * @throws AppException
     */
    protected function saveToFile($log): void
    {
        $file = fopen($this->getLogsFilePath(), 'ab+');
        fwrite($file, $log . "\n");
        fclose($file);
    }
}
