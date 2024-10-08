<?php

namespace WalkWeb\NW;

class Logger
{
    private const FILE_NAME = 'logs';

    /**
     * Логи
     *
     * @var string[]
     */
    private array $logs = [];

    /**
     * Место хранения файлов логов
     *
     * @var string
     */
    private string $dir;

    /**
     * Сохранять ли логи в файл
     *
     * @var bool
     */
    private bool $saveLog;

    public function __construct(bool $saveLog, string $dir)
    {
        $this->saveLog = $saveLog;
        $this->dir = $dir;
    }

    /**
     * Добавляет новый лог
     *
     * @param $log
     * @throws AppException
     */
    public function addLog($log): void
    {
        $this->logs[] = $log;

        if ($this->saveLog) {
            $this->saveToFile($log);
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

        return $this->dir . '/' . self::FILE_NAME;
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
