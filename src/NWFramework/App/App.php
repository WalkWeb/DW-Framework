<?php

namespace NW\App;

use Exception;
use NW\AppException;
use NW\Container;
use NW\Cookie;
use NW\Request\Request;
use NW\Response\Response;
use NW\Route\Router;
use NW\Utils\HttpCode;

class App
{
    private Router $router;
    private static ?Container $container = null;

    public function __construct(Router $router, ?Container $container = null)
    {
        $this->router = $router;
        self::$container = $container ?? new Container(
                APP_ENV,
                DB_HOST,
                DB_USER,
                DB_PASSWORD,
                DB_NAME,
                SAVE_LOG,
                LOG_DIR,
                LOG_FILE_NAME,
                CONTROLLERS_DIR,
            );
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
            throw new AppException('Отсутствует контроллер: ' . $handlerClass, HttpCode::INTERNAL_SERVER_ERROR);
        }

        $class = new $handlerClass(self::$container);

        if (!method_exists($class, $action)) {
            throw new AppException('Метод не найден: ' . $action, HttpCode::INTERNAL_SERVER_ERROR);
        }

        return $class->$action($request);
    }

    public function getContainer(): Container
    {
        return self::$container;
    }

    /**
     * Создает ответ сервера на основе Response
     *
     * @param Response $response
     * @throws AppException
     */
    public static function emit(Response $response): void
    {
        if (!self::$container) {
            throw new AppException('Метод emit не может вызываться до создания App');
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
        // TODO Получать корневую директорию с вьюхами из контейнера
        $view = __DIR__ . '/../../../views/default/errors/404.php';
        $content = file_exists($view) ? file_get_contents($view) : '404: Page not found';

        return new Response($content, HttpCode::NOT_FOUND);
    }
}
