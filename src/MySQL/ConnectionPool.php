<?php

declare(strict_types=1);

namespace WalkWeb\NW\MySQL;

use WalkWeb\NW\AppException;
use WalkWeb\NW\Container;

/**
 * ConnectionPool создан для того, чтобы можно было работать сразу с несколькими разными базами MySQL
 *
 * @package WalkWeb\NW
 */
class ConnectionPool
{
    public const DEFAULT = 'default';

    public const ERROR_INVALID_KEY    = 'Connection config key expected string';
    public const ERROR_INVALID_CONFIG = 'Connection config expected array';
    public const ERROR_INVALID_HOST   = 'Connection config[host] it required and type string';
    public const ERROR_INVALID_USER   = 'Connection config[user] it required and type string';
    public const ERROR_INVALID_PASS   = 'Connection config[password] it required and type string';
    public const ERROR_INVALID_DB     = 'Connection config[database] it required and type string';
    public const ERROR_MISS_CONFIG    = 'Connection config %s not specified';

    /**
     * @var Connection[]
     */
    private array $connections = [];

    private array $configs;
    private Container $container;

    /**
     * @param Container $container
     * @param array $configs
     * @throws AppException
     */
    public function __construct(Container $container, array $configs)
    {
        $this->validateConfigs($configs);
        $this->container = $container;
        $this->configs = $configs;
    }

    /**
     * Возвращает указанное подключение к базе. Если его нет - создает, если уже создано - возвращает существующее.
     * Если имя не указано - возвращает подключение по-умолчанию.
     *
     * @param string $name
     * @return Connection
     * @throws AppException
     */
    public function getConnection(string $name = self::DEFAULT): Connection
    {
        if (array_key_exists($name, $this->connections)) {
            return $this->connections[$name];
        }

        if (!array_key_exists($name, $this->configs)) {
            throw new AppException(sprintf(self::ERROR_MISS_CONFIG, $name));
        }

        $this->connections[$name] = $this->create($name);
        return $this->connections[$name];
    }

    /**
     * Возвращает суммарное количество запросов по всем подключениям.
     *
     * @return int
     */
    public function getCountQuery(): int
    {
        $count = 0;

        foreach ($this->connections as $connection) {
            $count += $connection->getCountQuery();
        }

        return $count;
    }

    /**
     * Возвращает все запросы по всем подключениям.
     *
     * @return array
     */
    public function getQueries(): array
    {
        $queries = [];

        foreach ($this->connections as $name => $connection) {
            $queries[$name] = $connection->getQueries();
        }

        return $queries;
    }

    /**
     * Создает новое подключение.
     *
     * @param string $name
     * @return Connection
     * @throws AppException
     */
    private function create(string $name): Connection
    {
        return new Connection(
            $this->configs[$name]['host'],
            $this->configs[$name]['user'],
            $this->configs[$name]['password'],
            $this->configs[$name]['database'],
            $this->container,
        );
    }

    /**
     * Валидирует массив параметров для подключений к базам
     *
     * @param array $configs
     * @throws AppException
     */
    private function validateConfigs(array $configs): void
    {
        foreach ($configs as $name => $config) {
            if (!is_string($name)) {
                throw new AppException(self::ERROR_INVALID_KEY);
            }
            if (!is_array($config)) {
                throw new AppException(self::ERROR_INVALID_CONFIG);
            }
            if (!array_key_exists('host', $config) || !is_string($config['host'])) {
                throw new AppException(self::ERROR_INVALID_HOST);
            }
            if (!array_key_exists('user', $config) || !is_string($config['user'])) {
                throw new AppException(self::ERROR_INVALID_USER);
            }
            if (!array_key_exists('password', $config) || !is_string($config['password'])) {
                throw new AppException(self::ERROR_INVALID_PASS);
            }
            if (!array_key_exists('database', $config) || !is_string($config['database'])) {
                throw new AppException(self::ERROR_INVALID_DB);
            }
        }
    }
}
