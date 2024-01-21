<?php

declare(strict_types=1);

namespace NW;

use NW\Request\Request;

class Container
{
    private array $map = [
        Connection::class => Connection::class,
        'connection'      => Connection::class,
        Logger::class     => Logger::class,
        'logger'          => Logger::class,
        Csrf::class       => Csrf::class,
        'csrf'            => Csrf::class,
        Captcha::class    => Captcha::class,
        'captcha'         => Captcha::class,
        Validator::class  => Validator::class,
        'validator'       => Validator::class,
        Request::class    => Request::class,
    ];

    private array $storage = [];

    private string $dbHost;
    private string $dbUser;
    private string $dbPassword;
    private string $dbName;
    private bool $loggerSaveLog;
    private string $loggerDir;
    private string $loggerFileName;
    private string $controllersDir;

    public function __construct(
        string $dbHost,
        string $dbUser,
        string $dbPassword,
        string $dbName,
        bool $loggerSaveLog,
        string $loggerDir,
        string $loggerFileName,
        string $controllersDir
    )
    {
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
        $this->loggerSaveLog = $loggerSaveLog;
        $this->loggerDir = $loggerDir;
        $this->loggerFileName = $loggerFileName;
        $this->controllersDir = $controllersDir;
    }

    /**
     * @param string $id
     * @return object
     * @throws AppException
     */
    public function get(string $id): object
    {
        $class = $this->getNameService($id);

        if ($this->exist($class)) {
            return $this->storage[$class];
        }

        if ($class === Request::class) {
            throw new AppException('Request не может быть создан автоматически, он должен быть добавлен в контейнер через set() вручную');
        }

        return $this->create($class);
    }

    /**
     * @param string $id
     * @param object $object
     * @throws AppException
     */
    public function set(string $id, object $object): void
    {
        $id = $this->getNameService($id);
        $this->storage[$id] = $object;
    }

    /**
     * @return Connection
     * @throws AppException
     */
    public function getConnection(): Connection
    {
        /** @var Connection $service */
        $service = $this->get(Connection::class);
        return $service;
    }

    /**
     * @return Logger
     * @throws AppException
     */
    public function getLogger(): Logger
    {
        /** @var Logger $service */
        $service = $this->get(Logger::class);
        return $service;
    }

    /**
     * @return Csrf
     * @throws AppException
     */
    public function getCsrf(): Csrf
    {
        /** @var Csrf $service */
        $service = $this->get(Csrf::class);
        return $service;
    }

    /**
     * @return Captcha
     * @throws AppException
     */
    public function getCaptcha(): Captcha
    {
        /** @var Captcha $service */
        $service = $this->get(Captcha::class);
        return $service;
    }

    /**
     * @return Validator
     * @throws AppException
     */
    public function getValidator(): Validator
    {
        /** @var Validator $service */
        $service = $this->get(Validator::class);
        return $service;
    }

    /**
     * @return Request
     * @throws AppException
     */
    public function getRequest(): Request
    {
        /** @var Request $service */
        $service = $this->get(Request::class);
        return $service;
    }

    /**
     * @return string
     */
    public function getControllersDir(): string
    {
        return $this->controllersDir;
    }

    /**
     * @param string $id
     * @return string
     * @throws AppException
     */
    private function getNameService(string $id): string
    {
        if (!array_key_exists($id, $this->map)) {
            throw new AppException('Unknown service: ' . $id);
        }

        return $this->map[$id];
    }

    /**
     * @param string $class
     * @return bool
     */
    public function exist(string $class): bool
    {
        try {
            $class = $this->getNameService($class);
            return array_key_exists($class, $this->storage);
        } catch (AppException $e) {
            // Контейнер может иметь только фиксированный набор сервисов. Если указан неизвестный - значит он не может
            // быть добавлен.
            return false;
        }
    }

    /**
     * Паттерн контейнер внедрения зависимостей, который автоматически, через рефлексию, определяет зависимости в
     * конструкторе и создает их не используется в целях максимальной производительности
     *
     * @param string $class
     * @return object
     * @throws AppException
     */
    private function create(string $class): object
    {
        if ($class === Connection::class) {
            $service = new Connection(
                $this->dbHost,
                $this->dbUser,
                $this->dbPassword,
                $this->dbName,
                $this
            );
        } elseif ($class === Logger::class) {
            $service = new Logger(
                $this->loggerSaveLog,
                $this->loggerDir,
                $this->loggerFileName,
            );
        } else {
            $service = new $class($this);
        }

        $this->storage[$this->map[$class]] = $service;
        return $service;
    }
}
