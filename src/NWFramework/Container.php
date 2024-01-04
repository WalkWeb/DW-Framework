<?php

declare(strict_types=1);

namespace NW;

class Container
{
    private array $map = [
        Connection::class => Connection::class,
        'connection'      => Connection::class,
    ];

    private array $storage = [];

    private string $dbHost;
    private string $dbUser;
    private string $dbPassword;
    private string $dbName;
    private string $controllersDir;

    public function __construct(string $dbHost, string $dbUser, string $dbPassword, string $dbName, string $controllersDir)
    {
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
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

        return $this->create($class);
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
            $object = new Connection(
                $this->dbHost,
                $this->dbUser,
                $this->dbPassword,
                $this->dbName,
            );

            $this->storage[$this->map[$class]] = $object;
            return $object;
        }

        $object = new $class($this);
        $this->storage[$this->map[$class]] = $object;
        return $object;
    }
}
