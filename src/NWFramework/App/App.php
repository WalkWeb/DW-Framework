<?php

namespace NW\App;

use Exception;
use NW\Request\Request;
use NW\Response\Response;
use NW\Route\Router;
use NW\Route\RouteCollection;
use NW\Route\RouteException;
use NW\Utils\HttpCode;

class App
{
    /**
     * @var RouteCollection
     */
    private $router;

    /**
     * @param Router $routes
     */
    public function __construct(Router $routes)
    {
        $this->router = $routes;
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
        } catch (RouteException $e) {

            // Если маршрут не найден, значит вызывается несуществующая страница
            return new Response($e->getMessage(), HttpCode::NOT_FOUND);
        }

        [$className, $action] = explode('@', $handler);

        $className = 'Controllers\\' . $className;

        if (!class_exists($className)) {
            return new Response(AppException::CONTROLLER_NOT_FOUND . ': ' . $className, HttpCode::INTERNAL_SERVER_ERROR);
        }

        $class = new $className();

        if (!method_exists($class, $action)) {
            throw new AppException(AppException::ACTION_NOT_FOUND);
        }

        return (new $className())->$action($request);
    }
}
