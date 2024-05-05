<?php

namespace WalkWeb\NW;

use Exception;
use WalkWeb\NW\Route\Router;

class App
{
    public const ERROR_MISS_CONTAINER     = 'The emit method cannot be called before the App is created';
    public const ERROR_MISS_HANDLER       = 'Handler missing: %s';
    public const ERROR_MISS_MIDDLEWARE    = 'Middleware missing: %s';
    public const ERROR_INVALID_MIDDLEWARE = 'Invalid middleware class: %s, expected extends AbstractMiddleware';

    public const TEMPLATE_500_PAGE     = '/default/errors/500.php';
    public const TEMPLATE_404_PAGE     = '/default/errors/404.php';

    private Router $router;
    private array $middleware;
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
            ['handler' => $handler, 'request' => $request, 'middleware' => $middleware] = $this->router->getHandler($request);
        } catch (Exception $e) {

            // Если маршрут не найден, значит вызывается несуществующая страница
            return $this->createNotFoundPage();
        }

        if (!class_exists($handler)) {
            throw new AppException(sprintf(self::ERROR_MISS_HANDLER, $handler));
        }

        return $this->handleRequest($request, $middleware, new $handler(self::$container));
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return self::$container;
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

        $view = self::$container->getViewDir() . self::TEMPLATE_500_PAGE;
        $content = file_exists($view) ? file_get_contents($view) : Response::DEFAULT_500_ERROR;
        return new Response($content, Response::INTERNAL_SERVER_ERROR);
    }

    /**
     * @return Response
     * @throws AppException
     */
    private function createNotFoundPage(): Response
    {
        $view = self::$container->getViewDir() . self::TEMPLATE_404_PAGE;
        $content = file_exists($view) ? file_get_contents($view) : Response::DEFAULT_404_ERROR;
        return new Response($content, Response::NOT_FOUND);
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
        $serverCookies = self::$container->getRequest()->getCookies()->getArray();
        $cookies = self::$container->getCookies()->getArray();

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
     * @param Request $request
     * @param array $middleware
     * @param callable $handler
     * @return Response
     * @throws AppException
     */
    private function handleRequest(Request $request, array $middleware, callable $handler): Response
    {
        $this->middleware = $this->createMiddleware($middleware);

        // Сортирует middleware по приоритету выполнения
        krsort($this->middleware);

        return $this->next($request, $handler);
    }

    /**
     * @param Request $request
     * @param callable $default
     * @return Response
     */
    private function next(Request $request, callable $default): Response
    {
        if (!$current = array_shift($this->middleware)) {
            return $default($request, $default);
        }

        return $current($request, function (Request $request) use ($default) {
            return $this->next($request, $default);
        });
    }

    /**
     * @param array $middlewares
     * @return array
     * @throws AppException
     */
    private function createMiddleware(array $middlewares): array
    {
        $classes = [];

        foreach ($middlewares as $priority => $className) {

            if (!class_exists($className)) {
                throw new AppException(sprintf(self::ERROR_MISS_MIDDLEWARE, $className));
            }

            $class = new $className(self::$container);

            if (!($class instanceof AbstractMiddleware)) {
                throw new AppException(sprintf(self::ERROR_INVALID_MIDDLEWARE, $className));
            }

            $classes[$priority] = $class;
        }

        return $classes;
    }
}
