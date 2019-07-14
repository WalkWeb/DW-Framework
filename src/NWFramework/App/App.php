<?php

namespace NW\App;

use NW\Request\Request;
use NW\Response\Response;
use NW\Route\Router;
use NW\Route\RouteCollection;
use NW\Route\RouteException;

class App
{
    /**
     * На основе запроса создает нужный контроллер и вызывает нужный его метод
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $router = new Router($this->getRoutes());

        try {
            ['handler' => $handler, 'request' => $request] = $router->getHandler($request);
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

    /**
     * Маршруты
     *
     * @return RouteCollection
     */
    private function getRoutes(): RouteCollection
    {
        // TODO Подумать над тем, куда можно вынести регистрацию маршрутов

        $routes = new RouteCollection();
        $routes->get('home', '/', 'MainController@index');
        $routes->get('post', '/post', 'PostController@index');

        return $routes;
    }

}
