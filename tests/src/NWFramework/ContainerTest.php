<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\AppException;
use NW\Connection;
use NW\Csrf;
use NW\Logger;
use Tests\AbstractTestCase;

class ContainerTest extends AbstractTestCase
{
    /**
     * @throws AppException
     */
    public function testContainerGetConnection(): void
    {
        $container = $this->getContainer();

        $connection = $container->get(Connection::class);
        self::assertInstanceOf(Connection::class, $connection);

        $connection = $container->get('connection');
        self::assertInstanceOf(Connection::class, $connection);

        $connection = $container->getConnection();
        self::assertInstanceOf(Connection::class, $connection);
    }

    /**
     * @throws AppException
     */
    public function testContainerGetLogger(): void
    {
        $container = $this->getContainer();

        $connection = $container->get(Logger::class);
        self::assertInstanceOf(Logger::class, $connection);

        $connection = $container->get('logger');
        self::assertInstanceOf(Logger::class, $connection);

        $connection = $container->getLogger();
        self::assertInstanceOf(Logger::class, $connection);
    }

    /**
     * @throws AppException
     */
    public function testContainerGetCsrf(): void
    {
        $container = $this->getContainer();

        $connection = $container->get(Csrf::class);
        self::assertInstanceOf(Csrf::class, $connection);

        $connection = $container->get('csrf');
        self::assertInstanceOf(Csrf::class, $connection);

        $connection = $container->getCsrf();
        self::assertInstanceOf(Csrf::class, $connection);
    }

    public function testContainerGetControllersDir(): void
    {
        self::assertEquals(CONTROLLERS_DIR, $this->getContainer()->getControllersDir());
    }

    public function testContainerUnknownService(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Unknown service: name_service');
        $this->getContainer()->get('name_service');
    }
}
