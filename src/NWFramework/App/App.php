<?php

namespace NW\App;

use Exception;
use NW\AppException;
use NW\Container;
use NW\Request\Request;
use NW\Response\Response;
use NW\Route\Router;
use NW\Utils\HttpCode;

class App
{
    private Router $router;
    private Container $container;

    public function __construct(Router $routes, ?Container $container = null)
    {
        $this->router = $routes;
        $this->container = $container ?? new Container(
                DB_HOST,
                DB_USER,
                DB_PASSWORD,
                DB_NAME,
            );
    }

    /**
     * На основе запроса создает нужный контроллер и вызывает нужный его метод
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
        try {
            ['handler' => $handler, 'request' => $request] = $this->router->getHandler($request);
        } catch (Exception $e) {

            // Если маршрут не найден, значит вызывается несуществующая страница
            return new Response($e->getMessage(), HttpCode::NOT_FOUND);
        }

        [$handlerClass, $action] = explode('@', $handler);

        $handlerClass = 'Controllers\\' . $handlerClass;

        if (!class_exists($handlerClass)) {
            return new Response('Отсутствует контроллер: ' . $handlerClass, HttpCode::INTERNAL_SERVER_ERROR);
        }

        $class = new $handlerClass($this->container);

        if (!method_exists($class, $action)) {
            throw new AppException('Метод не найден');
        }

        return $class->$action($request);
    }
}
