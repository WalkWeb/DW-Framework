<?php

declare(strict_types=1);

namespace Tests\src;

use stdClass;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Captcha;
use WalkWeb\NW\MySQL\ConnectionPool;
use WalkWeb\NW\Container;
use WalkWeb\NW\Cookie;
use WalkWeb\NW\Csrf;
use WalkWeb\NW\Logger;
use WalkWeb\NW\Mailer;
use WalkWeb\NW\Request;
use WalkWeb\NW\Runtime;
use WalkWeb\NW\Translation;
use WalkWeb\NW\Validator;
use Tests\AbstractTest;

class ContainerTest extends AbstractTest
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
            new Logger(SAVE_LOG, LOG_DIR),
            $container->getLogger()
        );
        self::assertEquals(new Csrf($container), $container->getCsrf());
        self::assertEquals(new Captcha($container), $container->getCaptcha());
        self::assertEquals(new Validator($container), $container->getValidator());
        self::assertEquals(CACHE_DIR, $container->getCacheDir());
        self::assertEquals(VIEW_DIR, $container->getViewDir());
        self::assertEquals(MIGRATION_DIR, $container->getMigrationDir());
        self::assertEquals(APP_ENV, $container->getAppEnv());

        // create manually
        $appEnv = Container::APP_PROD;
        $loggerSaveLog = false;
        $loggerDir = 'logger_dir';
        $cacheDir = 'cache_dir';
        $viewDir = 'view_dir';
        $migrationDir = 'migration_dir';
        $template = 'template';
        $translateDir = 'translate_dir';

        $container = Container::create(
            $appEnv,
            DB_CONFIGS,
            MAIL_CONFIG,
            $loggerSaveLog,
            $loggerDir,
            $cacheDir,
            $viewDir,
            $migrationDir,
            $template,
            $translateDir,
        );

        self::assertEquals(
            new Logger($loggerSaveLog, $loggerDir),
            $container->getLogger()
        );
        self::assertEquals(new Csrf($container), $container->getCsrf());
        self::assertEquals(new Captcha($container), $container->getCaptcha());
        self::assertEquals(new Validator($container), $container->getValidator());
        self::assertEquals($cacheDir, $container->getCacheDir());
        self::assertEquals($viewDir, $container->getViewDir());
        self::assertEquals($migrationDir, $container->getMigrationDir());
        self::assertEquals($appEnv, $container->getAppEnv());
        self::assertEquals($template, $container->getTemplate());
        self::assertEquals($translateDir, $container->getTranslateDir());
    }

    /**
     * Тест на ручное добавление сервиса в контейнер
     *
     * @throws AppException
     */
    public function testContainerSetService(): void
    {
        $logger = new Logger(SAVE_LOG, LOG_DIR);
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
    public function testContainerGetMailer(): void
    {
        $container = $this->getContainer();

        $mailer = $container->get(Mailer::class);
        self::assertInstanceOf(Mailer::class, $mailer);

        $mailer = $container->get('mailer');
        self::assertInstanceOf(Mailer::class, $mailer);

        $mailer = $container->getMailer();
        self::assertInstanceOf(Mailer::class, $mailer);
    }

    /**
     * @throws AppException
     */
    public function testContainerGetTranslation(): void
    {
        $container = $this->getContainer();

        $translation = $container->get(Translation::class);
        self::assertInstanceOf(Translation::class, $translation);

        $translation = $container->get('translation');
        self::assertInstanceOf(Translation::class, $translation);

        $translation = $container->getTranslation();
        self::assertInstanceOf(Translation::class, $translation);

        self::assertEquals(LANGUAGE, $translation->getLanguage());
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
        $user = new stdClass();

        self::assertFalse($container->exist('user'));

        $container->set('user', $user);

        self::assertTrue($container->exist('user'));
        self::assertEquals($user, $container->getUser());
    }

    /**
     * @throws AppException
     */
    public function testContainerUnset(): void
    {
        $container = $this->getContainer();
        $user = new stdClass();

        self::assertFalse($container->exist('user'));

        $container->set('user', $user);

        self::assertTrue($container->exist('user'));

        $container->unset('user');

        self::assertFalse($container->exist('user'));
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
