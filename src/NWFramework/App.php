<?php

namespace NW;

use Exception;
use NW\Route\Router;

class App
{
    public const ERROR_MISS_CONTAINER  = 'The emit method cannot be called before the App is created';
    public const ERROR_MISS_CONTROLLER = 'Controller missing: %s';
    public const ERROR_MISS_METHOD     = 'Controller method missing: %s';

    public const TEMPLATE_500_PAGE     = '/default/errors/500.php';
    public const TEMPLATE_404_PAGE     = '/default/errors/404.php';

    private Router $router;
    private static ?Container $container = null;

    public function __construct(Router $router, Container $container)
    {
        $this->router = $router;
        self::$container = $container;
    }

    /**
     * На основе запроса создает нужный контроллер и вызывает нужный его метод
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function handle(Request $request): Response
    {
        self::$container->set(Request::class, $request);
        self::$container->set(Cookie::class, clone $request->getCookies());

        try {
            ['handler' => $handler, 'request' => $request] = $this->router->getHandler($request);
        } catch (Exception $e) {

            // Если маршрут не найден, значит вызывается несуществующая страница
            return $this->createNotFoundPage();
        }

        [$handlerClass, $action] = explode('@', $handler);

        $handlerClass = self::$container->getControllersDir() . '\\' . $handlerClass;

        if (!class_exists($handlerClass)) {
            throw new AppException(sprintf(self::ERROR_MISS_CONTROLLER, $handlerClass), Response::INTERNAL_SERVER_ERROR);
        }

        $class = new $handlerClass(self::$container);

        if (!method_exists($class, $action)) {
            throw new AppException(sprintf(self::ERROR_MISS_METHOD, $action), Response::INTERNAL_SERVER_ERROR);
        }

        return $class->$action($request);
    }

    public function getContainer(): Container
    {
        return self::$container;
    }

    public static function createDefaultContainer(): Container
    {
        return new Container(
            APP_ENV,
            DB_HOST,
            DB_USER,
            DB_PASSWORD,
            DB_NAME,
            SAVE_LOG,
            LOG_DIR,
            LOG_FILE_NAME,
            CONTROLLERS_DIR,
            CACHE_DIR,
            VIEW_DIR,
        );
    }

    /**
     * @return Response
     * @throws AppException
     */
    public static function createInternalErrorResponse(): Response
    {
        if (self::$container === null) {
            throw new AppException(self::ERROR_MISS_CONTAINER);
        }

        $view = DIR . '/' . self::$container->getViewDir() . self::TEMPLATE_500_PAGE;
        $content = file_exists($view) ? file_get_contents($view) : Response::DEFAULT_500_ERROR;
        return new Response($content, Response::INTERNAL_SERVER_ERROR);
    }

    /**
     * Создает ответ сервера на основе Response
     *
     * @param Response $response
     * @throws AppException
     */
    public static function emit(Response $response): void
    {
        if (self::$container === null) {
            throw new AppException(self::ERROR_MISS_CONTAINER);
        }

        if (self::$container->getAppEnv() !== Container::APP_TEST) {
            self::saveHeader($response);
            self::saveCookies();
        }

        echo $response->getBody();
    }

    private static function saveHeader(Response $response): void
    {
        header(sprintf('HTTP/%s %d', $response->getProtocolVersion(), $response->getStatusCode()));

        foreach ($response->getHeaders() as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    /**
     * @throws AppException
     */
    private static function saveCookies(): void
    {
        $serverCookies = self::$container->getRequest()->getCookies()->getCookies();
        $cookies = self::$container->getCookies()->getCookies();

        foreach ($cookies as $key => $value) {
            if (!array_key_exists($key, $serverCookies)) {

                setcookie($key, $value, time() + 31104000, '/');
            }
            elseif ($serverCookies[$key] !== $cookies[$key]) {
                setcookie($key, $value, time() + 31104000, '/');
            }
        }

        foreach ($serverCookies as $aKey => $aValue) {
            if (!array_key_exists($aKey, $cookies)) {
                setcookie($aKey, '', -1, '/');
            }
        }
    }

    /**
     * @return Response
     * @throws AppException
     */
    private function createNotFoundPage(): Response
    {
        $view = DIR . '/' . $this->getContainer()->getViewDir() . self::TEMPLATE_404_PAGE;
        $content = file_exists($view) ? file_get_contents($view) : Response::DEFAULT_404_ERROR;

        return new Response($content, Response::NOT_FOUND);
    }
}
