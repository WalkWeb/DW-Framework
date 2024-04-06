<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Models\User\UserFactory;
use NW\AppException;
use NW\Captcha;
use NW\ConnectionPool;
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
            new ConnectionPool($container, DB_CONFIGS),
            $container->getConnectionPool()
        );
        self::assertEquals(
            new Logger(SAVE_LOG, LOG_DIR, LOG_FILE_NAME),
            $container->getLogger()
        );
        self::assertEquals(new Csrf(), $container->getCsrf());
        self::assertEquals(new Captcha($container), $container->getCaptcha());
        self::assertEquals(new Validator($container), $container->getValidator());
        self::assertEquals(HANDLERS_DIR, $container->getHandlersDir());
        self::assertEquals(MIDDLEWARE_DIR, $container->getMiddlewareDir());
        self::assertEquals(CACHE_DIR, $container->getCacheDir());
        self::assertEquals(VIEW_DIR, $container->getViewDir());
        self::assertEquals(APP_ENV, $container->getAppEnv());

        // create manually
        $appEnv = Container::APP_PROD;
        $loggerSaveLog = false;
        $loggerDir = 'logger_dir';
        $loggerFileName = 'logger_file_name';
        $handlersDir = 'handlers_dir';
        $middlewareDir = 'middleware_dir';
        $cacheDir = 'cache_dir';
        $viewDir = 'view_dir';
        $template = 'template';

        $container = Container::create(
            $appEnv,
            DB_CONFIGS,
            $loggerSaveLog,
            $loggerDir,
            $loggerFileName,
            $handlersDir,
            $middlewareDir,
            $cacheDir,
            $viewDir,
            $template,
        );

        self::assertEquals(
            new Logger($loggerSaveLog, $loggerDir, $loggerFileName),
            $container->getLogger()
        );
        self::assertEquals(new Csrf(), $container->getCsrf());
        self::assertEquals(new Captcha($container), $container->getCaptcha());
        self::assertEquals(new Validator($container), $container->getValidator());
        self::assertEquals($handlersDir, $container->getHandlersDir());
        self::assertEquals($middlewareDir, $container->getMiddlewareDir());
        self::assertEquals($cacheDir, $container->getCacheDir());
        self::assertEquals($viewDir, $container->getViewDir());
        self::assertEquals($appEnv, $container->getAppEnv());
        self::assertEquals($template, $container->getTemplate());
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
    public function testContainerGetConnectionPool(): void
    {
        $container = $this->getContainer();

        $connectionPool = $container->get(ConnectionPool::class);
        self::assertInstanceOf(ConnectionPool::class, $connectionPool);

        $connectionPool = $container->get('connection_pool');
        self::assertInstanceOf(ConnectionPool::class, $connectionPool);

        $connectionPool = $container->getConnectionPool();
        self::assertInstanceOf(ConnectionPool::class, $connectionPool);
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
     * @throws AppException
     */
    public function testContainerGetUserNotSet(): void
    {
        $container = $this->getContainer();

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(sprintf(Container::GET_ERROR, 'user'));
        $container->getUser();
    }

    /**
     * @throws AppException
     */
    public function testContainerGetUserSuccess(): void
    {
        $container = $this->getContainer();
        $user = UserFactory::createNew(
            ['login' => 'Login', 'email' => 'email@email.com', 'password' => '12345'],
            'hash_key',
            'default',
        );

        self::assertFalse($container->exist('user'));

        $container->set('user', $user);

        self::assertTrue($container->exist('user'));
        self::assertEquals($user, $container->getUser());
    }

    /**
     * Тест на ситуацию, когда запрашиваются сервисы Request/Cookie/Runtime до того, как они установлены через set()
     *
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
     * Аналогично testContainerGetServiceFail, только запрос идет к конкретному методу на получение сервиса
     *
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

    /**
     * Тест на установку нового template
     *
     * @throws AppException
     */
    public function testContainerSetTemplate(): void
    {
        $container = Container::create();

        self::assertEquals(TEMPLATE_DEFAULT, $container->getTemplate());

        $template = 'new_template';
        $container->setTemplate($template);

        self::assertEquals($template, $container->getTemplate());
    }

    /**
     * Тест на ситуацию, когда запрашивается неизвестный сервис
     *
     * @throws AppException
     */
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

    /**
     * @return array
     */
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

    /**
     * @return array
     */
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
