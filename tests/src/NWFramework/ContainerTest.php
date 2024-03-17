<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\AppException;
use NW\Captcha;
use NW\Connection;
use NW\Container;
use NW\Cookie;
use NW\Csrf;
use NW\Logger;
use NW\Request;
use NW\Runtime;
use NW\Validator;
use Tests\AbstractTestCase;

class ContainerTest extends AbstractTestCase
{
    /**
     * @throws AppException
     */
    public function testContainerCreate(): void
    {
        // create default
        $container = Container::create();

        self::assertEquals(
            new Connection(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $container),
            $container->getConnection()
        );
        self::assertEquals(
            new Logger(SAVE_LOG, LOG_DIR, LOG_FILE_NAME),
            $container->getLogger()
        );
        self::assertEquals(new Csrf(), $container->getCsrf());
        self::assertEquals(new Captcha(), $container->getCaptcha());
        self::assertEquals(new Validator($container), $container->getValidator());
        self::assertEquals(CONTROLLERS_DIR, $container->getControllersDir());
        self::assertEquals(CACHE_DIR, $container->getCacheDir());
        self::assertEquals(VIEW_DIR, $container->getViewDir());
        self::assertEquals(APP_ENV, $container->getAppEnv());

        // create manually
        $appEnv = Container::APP_PROD;
        $loggerSaveLog = false;
        $loggerDir = 'logger_dir';
        $loggerFileName = 'logger_file_name';
        $controllersDir = 'controllers_dir';
        $cacheDir = 'cache_dir';
        $viewDir = 'view_dir';

        $container = Container::create(
            $appEnv,
            DB_HOST,
            DB_USER,
            DB_PASSWORD,
            DB_NAME,
            $loggerSaveLog,
            $loggerDir,
            $loggerFileName,
            $controllersDir,
            $cacheDir,
            $viewDir,
        );

        self::assertEquals(
            new Logger($loggerSaveLog, $loggerDir, $loggerFileName),
            $container->getLogger()
        );
        self::assertEquals(new Csrf(), $container->getCsrf());
        self::assertEquals(new Captcha(), $container->getCaptcha());
        self::assertEquals(new Validator($container), $container->getValidator());
        self::assertEquals($controllersDir, $container->getControllersDir());
        self::assertEquals($cacheDir, $container->getCacheDir());
        self::assertEquals($viewDir, $container->getViewDir());
        self::assertEquals($appEnv, $container->getAppEnv());
    }

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
    public function testContainerGetCookies(): void
    {
        $container = $this->getContainer();
        $cookie = new Cookie();

        $container->set(Cookie::class, $cookie);

        self::assertEquals($cookie, $container->getCookies());
    }

    /**
     * @throws AppException
     */
    public function testContainerGetRuntime(): void
    {
        $container = $this->getContainer();
        $cookie = new Runtime();

        $container->set(Runtime::class, $cookie);

        self::assertEquals($cookie, $container->getRuntime());
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
     * @dataProvider getServiceErrorDataProvider
     * @param string $class
     * @param string $error
     * @throws AppException
     */
    public function testContainerGetServiceFail(string $class, string $error): void
    {
        $container = Container::create();

        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        $container->get($class);
    }

    /**
     * @dataProvider getMethodServiceErrorDataProvider
     * @param string $method
     * @param string $error
     * @throws AppException
     */
    public function testContainerGetMethodServiceFail(string $method, string $error): void
    {
        $container = Container::create();

        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        $container->$method();
    }

    /**
     * @throws AppException
     */
    public function testContainerGetControllersDir(): void
    {
        self::assertEquals(CONTROLLERS_DIR, $this->getContainer()->getControllersDir());
    }

    /**
     * @throws AppException
     */
    public function testContainerGetCacheDir(): void
    {
        self::assertEquals(CACHE_DIR, $this->getContainer()->getCacheDir());
    }

    /**
     * @throws AppException
     */
    public function testContainerGetViewDir(): void
    {
        self::assertEquals(VIEW_DIR, $this->getContainer()->getViewDir());
    }

    /**
     * Тест на успешную установку APP_ENV
     *
     * @throws AppException
     */
    public function testContainerSetAppEnvSuccess(): void
    {
        self::assertEquals(Container::APP_TEST, $this->getContainer()->getAppEnv());
    }

    /**
     * Тест на попытку указать некорректный APP_ENV
     *
     * @throws AppException
     */
    public function testContainerSetAppEnvFail(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Invalid APP_ENV. Valid values: prod, dev, test');
        $this->getContainer('invalid_app_env');
    }

    public function testContainerUnknownService(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Unknown service: name_service');
        $this->getContainer()->get('name_service');
    }

    /**
     * @throws AppException
     */
    public function testContainerExistService(): void
    {
        $container = $this->getContainer();

        $container->get(Validator::class);

        self::assertTrue($container->exist(Validator::class));
        self::assertTrue($container->exist('validator'));
    }

    /**
     * @throws AppException
     */
    public function testContainerNoExistService(): void
    {
        self::assertFalse($this->getContainer()->exist('UnknownService'));
    }

    public function getServiceErrorDataProvider(): array
    {
        return [
            [
                Request::class,
                sprintf(Container::GET_ERROR, 'Request'),
            ],
            [
                Cookie::class,
                sprintf(Container::GET_ERROR, 'Cookie'),
            ],
            [
                Runtime::class,
                sprintf(Container::GET_ERROR, 'Runtime'),
            ],
        ];
    }

    public function getMethodServiceErrorDataProvider(): array
    {
        return [
            [
                'getRequest',
                sprintf(Container::GET_ERROR, 'Request'),
            ],
            [
                'getCookies',
                sprintf(Container::GET_ERROR, 'Cookie'),
            ],
            [
                'getRuntime',
                sprintf(Container::GET_ERROR, 'Runtime'),
            ],
        ];
    }
}
