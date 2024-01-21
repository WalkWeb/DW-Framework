<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\AppException;
use NW\Captcha;
use NW\Connection;
use NW\Csrf;
use NW\Logger;
use NW\Validator;
use Tests\AbstractTestCase;

class ContainerTest extends AbstractTestCase
{
    /**
     * Тест на ручное добавление сервиса в контейнер
     *
     * @throws AppException
     */
    public function testContainerSetService(): void
    {
        $logger = new Logger(SAVE_LOG, LOG_DIR, LOG_FILE_NAME);
        $logger->addLog('abc');

        $container = $this->getContainer();
        $container->set(Logger::class, $logger);

        self::assertEquals($logger, $container->getLogger());
    }

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

    /**
     * @throws AppException
     */
    public function testContainerGetCaptcha(): void
    {
        $container = $this->getContainer();

        $connection = $container->get(Captcha::class);
        self::assertInstanceOf(Captcha::class, $connection);

        $connection = $container->get('captcha');
        self::assertInstanceOf(Captcha::class, $connection);

        $connection = $container->getCaptcha();
        self::assertInstanceOf(Captcha::class, $connection);
    }

    /**
     * @throws AppException
     */
    public function testContainerGetValidator(): void
    {
        $container = $this->getContainer();

        $connection = $container->get(Validator::class);
        self::assertInstanceOf(Validator::class, $connection);

        $connection = $container->get('validator');
        self::assertInstanceOf(Validator::class, $connection);

        $connection = $container->getValidator();
        self::assertInstanceOf(Validator::class, $connection);
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
