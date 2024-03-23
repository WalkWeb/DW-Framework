<?php

declare(strict_types=1);

namespace NW;

class Container
{
    public const APP_PROD = 'prod';
    public const APP_DEV  = 'dev';
    public const APP_TEST = 'test';

    public const GET_ERROR = '%s cannot be created automatically, it must be added to the container via set() manually';

    private array $map = [
        Connection::class => Connection::class,
        'connection'      => Connection::class,
        Logger::class     => Logger::class,
        'logger'          => Logger::class,
        Csrf::class       => Csrf::class,
        'csrf'            => Csrf::class,
        Captcha::class    => Captcha::class,
        'captcha'         => Captcha::class,
        Validator::class  => Validator::class,
        'validator'       => Validator::class,
        Request::class    => Request::class,
        Cookie::class    => Cookie::class,
        Runtime::class    => Runtime::class,
    ];

    private array $storage = [];

    private string $appEnv;
    private string $dbHost;
    private string $dbUser;
    private string $dbPassword;
    private string $dbName;
    private bool $loggerSaveLog;
    private string $loggerDir;
    private string $loggerFileName;
    private string $handlersDir;
    private string $cacheDir;
    private string $viewDir;

    /**
     * @param string $appEnv
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $dbName
     * @param bool $loggerSaveLog
     * @param string $loggerDir
     * @param string $loggerFileName
     * @param string $handlersDir
     * @param string $cacheDir
     * @param string $viewDir
     * @throws AppException
     */
    public function __construct(
        string $appEnv,
        string $dbHost,
        string $dbUser,
        string $dbPassword,
        string $dbName,
        bool $loggerSaveLog,
        string $loggerDir,
        string $loggerFileName,
        string $handlersDir,
        string $cacheDir,
        string $viewDir
    )
    {
        $this->setAppEnv($appEnv);
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
        $this->loggerSaveLog = $loggerSaveLog;
        $this->loggerDir = $loggerDir;
        $this->loggerFileName = $loggerFileName;
        $this->handlersDir = $handlersDir;
        $this->cacheDir = $cacheDir;
        $this->viewDir = $viewDir;
    }

    /**
     * @param string $appEnv
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $dbName
     * @param bool $loggerSaveLog
     * @param string $loggerDir
     * @param string $loggerFileName
     * @param string $handlersDir
     * @param string $cacheDir
     * @param string $viewDir
     * @return static
     * @throws AppException
     */
    public static function create(
        string $appEnv = APP_ENV,
        string $dbHost = DB_HOST,
        string $dbUser = DB_USER,
        string $dbPassword = DB_PASSWORD,
        string $dbName = DB_NAME,
        bool $loggerSaveLog = SAVE_LOG,
        string $loggerDir = LOG_DIR,
        string $loggerFileName = LOG_FILE_NAME,
        string $handlersDir = HANDLERS_DIR,
        string $cacheDir = CACHE_DIR,
        string $viewDir = VIEW_DIR
    ): self
    {
        return new self(
            $appEnv,
            $dbHost,
            $dbUser,
            $dbPassword,
            $dbName,
            $loggerSaveLog,
            $loggerDir,
            $loggerFileName,
            $handlersDir,
            $cacheDir,
            $viewDir
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

        if ($class === Request::class || $class === Cookie::class || $class === Runtime::class) {
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
     * @return Connection
     * @throws AppException
     */
    public function getConnection(): Connection
    {
        /** @var Connection $service */
        $service = $this->get(Connection::class);
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
     * @return string
     */
    public function getHandlersDir(): string
    {
        return $this->handlersDir;
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
    public function getAppEnv(): string
    {
        return $this->appEnv;
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
        if ($class === Connection::class) {
            $service = new Connection(
                $this->dbHost,
                $this->dbUser,
                $this->dbPassword,
                $this->dbName,
                $this
            );
        } elseif ($class === Logger::class) {
            $service = new Logger(
                $this->loggerSaveLog,
                $this->loggerDir,
                $this->loggerFileName,
            );
        } else {
            $service = new $class($this);
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
