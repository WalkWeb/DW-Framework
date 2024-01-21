<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\AppException;
use NW\Captcha;
use NW\Connection;
use NW\Csrf;
use NW\Logger;
use NW\Request\Request;
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

        $logger = $container->get(Logger::class);
        self::assertInstanceOf(Logger::class, $logger);

        $logger = $container->get('logger');
        self::assertInstanceOf(Logger::class, $logger);

        $logger = $container->getLogger();
        self::assertInstanceOf(Logger::class, $logger);
    }

    /**
     * @throws AppException
     */
    public function testContainerGetCsrf(): void
    {
        $container = $this->getContainer();

        $csrf = $container->get(Csrf::class);
        self::assertInstanceOf(Csrf::class, $csrf);

        $csrf = $container->get('csrf');
        self::assertInstanceOf(Csrf::class, $csrf);

        $csrf = $container->getCsrf();
        self::assertInstanceOf(Csrf::class, $csrf);
    }

    /**
     * @throws AppException
     */
    public function testContainerGetCaptcha(): void
    {
        $container = $this->getContainer();

        $captcha = $container->get(Captcha::class);
        self::assertInstanceOf(Captcha::class, $captcha);

        $captcha = $container->get('captcha');
        self::assertInstanceOf(Captcha::class, $captcha);

        $captcha = $container->getCaptcha();
        self::assertInstanceOf(Captcha::class, $captcha);
    }

    /**
     * @throws AppException
     */
    public function testContainerGetValidator(): void
    {
        $container = $this->getContainer();

        $validator = $container->get(Validator::class);
        self::assertInstanceOf(Validator::class, $validator);

        $validator = $container->get('validator');
        self::assertInstanceOf(Validator::class, $validator);

        $validator = $container->getValidator();
        self::assertInstanceOf(Validator::class, $validator);
    }

    /**
     * @throws AppException
     */
    public function testContainerGetRequestSuccess(): void
    {
        $container = $this->getContainer();
        $request = new Request(['example' => 123]);

        $container->set(Request::class, $request);

        self::assertEquals($request, $container->getRequest());
    }

    /**
     * @throws AppException
     */
    public function testContainerGetFail(): void
    {
        $container = $this->getContainer();

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Request не может быть создан автоматически, он должен быть добавлен в контейнер через set() вручную');
        $container->get(Request::class);

    }

    /**
     * @throws AppException
     */
    public function testContainerGetRequestFail(): void
    {
        $container = $this->getContainer();

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Request не может быть создан автоматически, он должен быть добавлен в контейнер через set() вручную');
        $container->getRequest();
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
