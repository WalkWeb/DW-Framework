<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\AppException;
use NW\Connection;
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

    public function testContainerUnknownService(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Unknown service: name_service');
        $this->getContainer()->get('name_service');
    }
}
