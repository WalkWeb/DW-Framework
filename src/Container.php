<?php

declare(strict_types=1);

namespace WalkWeb\NW;

use WalkWeb\NW\MySQL\ConnectionPool;

class Container
{
    public const APP_PROD = 'prod';
    public const APP_DEV = 'dev';
    public const APP_TEST = 'test';

    public const GET_ERROR = '%s cannot be created automatically, it must be added to the container via set() manually';

    private array $map = [
        ConnectionPool::class => ConnectionPool::class,
        'connection_pool'     => ConnectionPool::class,
        Logger::class         => Logger::class,
        'logger'              => Logger::class,
        Csrf::class           => Csrf::class,
        'csrf'                => Csrf::class,
        Captcha::class        => Captcha::class,
        'captcha'             => Captcha::class,
        Validator::class      => Validator::class,
        'validator'           => Validator::class,
        Mailer::class         => Mailer::class,
        'mailer'              => Mailer::class,
        Translation::class    => Translation::class,
        'translation'         => Translation::class,
        Request::class        => Request::class,
        Cookie::class         => Cookie::class,
        Runtime::class        => Runtime::class,

        // User this is any custom object
        'user'                => 'user',
    ];

    private array $storage = [];

    private string $appEnv;
    private array $dbConfigs;
    private array $mailerConfig;
    private bool $loggerSaveLog;
    private string $loggerDir;
    private string $cacheDir;
    private string $viewDir;
    private string $migrationDir;
    private string $template;
    private string $translateDir;
    private string $language;

    /**
     * @param string $appEnv
     * @param array $dbConfigs
     * @param array $mailerConfig
     * @param bool $loggerSaveLog
     * @param string $loggerDir
     * @param string $cacheDir
     * @param string $viewDir
     * @param string $migrationDir
     * @param string $template
     * @param string $translateDir
     * @param string $language
     * @throws AppException
     */
    public function __construct(
        string $appEnv,
        array $dbConfigs,
        array $mailerConfig,
        bool $loggerSaveLog,
        string $loggerDir,
        string $cacheDir,
        string $viewDir,
        string $migrationDir,
        string $template,
        string $translateDir,
        string $language
    )
    {
        $this->setAppEnv($appEnv);
        $this->dbConfigs = $dbConfigs;
        $this->mailerConfig = $mailerConfig;
        $this->loggerSaveLog = $loggerSaveLog;
        $this->loggerDir = $loggerDir;
        $this->cacheDir = $cacheDir;
        $this->viewDir = $viewDir;
        $this->migrationDir = $migrationDir;
        $this->template = $template;
        $this->translateDir = $translateDir;
        $this->language = $language;
    }

    /**
     * @param string $appEnv
     * @param array $dbConfigs
     * @param array $mailerConfig
     * @param bool $loggerSaveLog
     * @param string $loggerDir
     * @param string $cacheDir
     * @param string $viewDir
     * @param string $migrationDir
     * @param string $template
     * @param string $translateDir
     * @param string $language
     * @return static
     * @throws AppException
     */
    public static function create(
        string $appEnv = APP_ENV,
        array $dbConfigs = DB_CONFIGS,
        array $mailerConfig = MAIL_CONFIG,
        bool $loggerSaveLog = SAVE_LOG,
        string $loggerDir = LOG_DIR,
        string $cacheDir = CACHE_DIR,
        string $viewDir = VIEW_DIR,
        string $migrationDir = MIGRATION_DIR,
        string $template = TEMPLATE_DEFAULT,
        string $translateDir = TRANSLATE_DIR,
        string $language = LANGUAGE
    ): self
    {
        return new self(
            $appEnv,
            $dbConfigs,
            $mailerConfig,
            $loggerSaveLog,
            $loggerDir,
            $cacheDir,
            $viewDir,
            $migrationDir,
            $template,
            $translateDir,
            $language
        );
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

        if ($class === Request::class || $class === Cookie::class || $class === Runtime::class || $class === 'user') {
            throw new AppException(
                sprintf(self::GET_ERROR, $class)
            );
        }

        return $this->createService($class);
    }

    /**
     * @param string $id
     * @param object $object
     * @throws AppException
     */
    public function set(string $id, object $object): void
    {
        $id = $this->getNameService($id);
        $this->storage[$id] = $object;
    }

    /**
     * @param string $id
     * @throws AppException
     */
    public function unset(string $id): void
    {
        $id = $this->getNameService($id);
        unset($this->storage[$id]);
    }

    /**
     * @return ConnectionPool
     * @throws AppException
     */
    public function getConnectionPool(): ConnectionPool
    {
        /** @var ConnectionPool $service */
        $service = $this->get(ConnectionPool::class);
        return $service;
    }

    /**
     * @return Logger
     * @throws AppException
     */
    public function getLogger(): Logger
    {
        /** @var Logger $service */
        $service = $this->get(Logger::class);
        return $service;
    }

    /**
     * @return Csrf
     * @throws AppException
     */
    public function getCsrf(): Csrf
    {
        /** @var Csrf $service */
        $service = $this->get(Csrf::class);
        return $service;
    }

    /**
     * @return Captcha
     * @throws AppException
     */
    public function getCaptcha(): Captcha
    {
        /** @var Captcha $service */
        $service = $this->get(Captcha::class);
        return $service;
    }

    /**
     * @return Validator
     * @throws AppException
     */
    public function getValidator(): Validator
    {
        /** @var Validator $service */
        $service = $this->get(Validator::class);
        return $service;
    }

    /**
     * @return Request
     * @throws AppException
     */
    public function getRequest(): Request
    {
        /** @var Request $service */
        $service = $this->get(Request::class);
        return $service;
    }

    /**
     * @return Cookie
     * @throws AppException
     */
    public function getCookies(): Cookie
    {
        /** @var Cookie $service */
        $service = $this->get(Cookie::class);
        return $service;
    }

    /**
     * @return Runtime
     * @throws AppException
     */
    public function getRuntime(): Runtime
    {
        /** @var Runtime $service */
        $service = $this->get(Runtime::class);
        return $service;
    }

    /**
     * @return Mailer
     * @throws AppException
     */
    public function getMailer(): Mailer
    {
        /** @var Mailer $service */
        $service = $this->get(Mailer::class);
        return $service;
    }

    /**
     * @return Translation
     * @throws AppException
     */
    public function getTranslation(): Translation
    {
        /** @var Translation $service */
        $service = $this->get(Translation::class);
        return $service;
    }

    /**
     * @return object
     * @throws AppException
     */
    public function getUser(): object
    {
        return $this->get('user');
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * @return string
     */
    public function getViewDir(): string
    {
        return $this->viewDir;
    }

    /**
     * @return string
     */
    public function getMigrationDir(): string
    {
        return $this->migrationDir;
    }

    /**
     * @return string
     */
    public function getAppEnv(): string
    {
        return $this->appEnv;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getTranslateDir(): string
    {
        return $this->translateDir;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
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
    private function createService(string $class): object
    {
        switch ($class) {
            case ConnectionPool::class:
                $service = new ConnectionPool($this, $this->dbConfigs);
                break;
            case Mailer::class:
                $service = new Mailer($this, $this->mailerConfig);
                break;
            case Logger::class:
                $service = new Logger($this->loggerSaveLog, $this->loggerDir);
                break;
            case Translation::class:
                $service = new Translation($this, $this->language);
                break;
            default:
                $service = new $class($this);
                break;
        }

        $this->storage[$this->map[$class]] = $service;
        return $service;
    }

    /**
     * @param string $appEnv
     * @throws AppException
     */
    private function setAppEnv(string $appEnv): void
    {
        if ($appEnv !== self::APP_PROD && $appEnv !== self::APP_DEV && $appEnv !== self::APP_TEST) {
            throw new AppException(
                'Invalid APP_ENV. Valid values: ' . self::APP_PROD . ', ' . self::APP_DEV . ', ' . self::APP_TEST
            );
        }

        $this->appEnv = $appEnv;
    }
}
