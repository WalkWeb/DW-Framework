<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\MySQL;

use NW\AppException;
use NW\MySQL\ConnectionPool;
use Tests\AbstractTest;

class ConnectionPoolTest extends AbstractTest
{
    /**
     * Тест на создание пула подключений.
     *
     * @dataProvider successDataProvider
     * @param array $configs
     * @throws AppException
     */
    public function testConnectionPoolCreateSuccess(array $configs): void
    {
        $pool = new ConnectionPool($this->getContainer(), $configs);

        self::assertEquals(0, $pool->getCountQuery());
        self::assertEquals([], $pool->getQueries());
    }

    /**
     * Тест на создание пула подключений и получение подключения.
     *
     * @throws AppException
     */
    public function testConnectionPoolCreateAndGet(): void
    {
        $pool = new ConnectionPool($this->getContainer(), DB_CONFIGS);

        $connection1 = $pool->getConnection();
        $connection2 = $pool->getConnection();

        self::assertEquals($connection1, $connection2);

        self::assertEquals(0, $pool->getCountQuery());
        self::assertEquals(['default' => []], $pool->getQueries());
    }

    /**
     * Тест на ситуацию, когда запрашивается подключение по которому не были переданы данные.
     *
     * @dataProvider successDataProvider
     * @param array $configs
     * @throws AppException
     */
    public function testConnectionPoolMissConfig(array $configs): void
    {
        $connectionName = 'miss';
        $pool = new ConnectionPool($this->getContainer(), $configs);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(sprintf(ConnectionPool::ERROR_MISS_CONFIG, $connectionName));
        $pool->getConnection($connectionName);
    }

    /**
     * Тест на различные варианты невалидных конфигов.
     *
     * @dataProvider invalidConfigsDataProvider
     * @param array $configs
     * @param string $error
     * @throws AppException
     */
    public function testConnectionPoolInvalidConfigs(array $configs, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        new ConnectionPool($this->getContainer(), $configs);
    }

    /**
     * @return array
     */
    public function successDataProvider(): array
    {
        return [
            // Вариант с одним подключением
            [
                [
                    'default' => [
                        'host'     => 'host',
                        'user'     => 'user',
                        'password' => 'password',
                        'database' => 'database',
                    ],
                ],
            ],
            // Вариант без подключений (допускаем, что приложение может работать и без базы)
            [
                [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidConfigsDataProvider(): array
    {
        return [
            // key не string
            [
                [
                    0 => [
                        'host'     => 'host',
                        'user'     => 'user',
                        'password' => 'password',
                        'database' => 'database',
                    ],
                ],
                ConnectionPool::ERROR_INVALID_KEY,
            ],
            // Конфиг параметров не массив
            [
                [
                    'default' => 'abc',
                ],
                ConnectionPool::ERROR_INVALID_CONFIG,
            ],
            // Отсутствует host
            [
                [
                    'default' => [
                        'user'     => 'user',
                        'password' => 'password',
                        'database' => 'database',
                    ],
                ],
                ConnectionPool::ERROR_INVALID_HOST,
            ],
            // host некорректного типа
            [
                [
                    'default' => [
                        'host'     => 123,
                        'user'     => 'user',
                        'password' => 'password',
                        'database' => 'database',
                    ],
                ],
                ConnectionPool::ERROR_INVALID_HOST,
            ],
            // Отсутствует user
            [
                [
                    'default' => [
                        'host' => 'host',
                        'password' => 'password',
                        'database' => 'database',
                    ],
                ],
                ConnectionPool::ERROR_INVALID_USER,
            ],
            // user некорректного типа
            [
                [
                    'default' => [
                        'host' => 'host',
                        'user' => null,
                        'password' => 'password',
                        'database' => 'database',
                    ],
                ],
                ConnectionPool::ERROR_INVALID_USER,
            ],
            // Отсутствует password
            [
                [
                    'default' => [
                        'host' => 'host',
                        'user' => 'user',
                        'database' => 'database',
                    ],
                ],
                ConnectionPool::ERROR_INVALID_PASS,
            ],
            // password некорректного типа
            [
                [
                    'default' => [
                        'host' => 'host',
                        'user' => 'user',
                        'password' => 1.4,
                        'database' => 'database',
                    ],
                ],
                ConnectionPool::ERROR_INVALID_PASS,
            ],
            // Отсутствует database
            [
                [
                    'default' => [
                        'host' => 'host',
                        'user' => 'user',
                        'password' => 'password',
                    ],
                ],
                ConnectionPool::ERROR_INVALID_DB,
            ],
            // database некорректного типа
            [
                [
                    'default' => [
                        'host' => 'host',
                        'user' => 'user',
                        'password' => 'password',
                        'database' => [],
                    ],
                ],
                ConnectionPool::ERROR_INVALID_DB,
            ],
        ];
    }
}
