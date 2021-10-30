<?php

namespace NW\App;

use Exception;
use NW\Request\Request;
use NW\Response\Response;
use NW\Route\Router;
use NW\Route\RouteCollection;
use NW\Route\RouteException;

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
            return new Response($e->getMessage(), 404);
        }

        [0 => $class, 1 => $action] = explode('@', $handler);

        $class = 'Controllers\\' . $class;

        if (!class_exists($class)) {
            return new Response('Отсутствует контроллер ' . $class, 500);
        }

        // TODO проверка на наличие нужного метода в контроллере

        return (new $class())->$action($request);
    }
}
