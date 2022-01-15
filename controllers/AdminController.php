<?php

namespace Controllers;

use NW\AbstractController;
use Middleware\AuthMiddleware;
use NW\Response\Response;

class AdminController extends AbstractController
{
    public function __construct()
    {
        // Подключить middleware можно и через конструктор контроллера
        $this->middleware[] = AuthMiddleware::class;
        parent::__construct();
    }

    public function index(): Response
    {
        // К этому маршруту привязан AuthMiddleware, задача которого - бросить исключение.
        // До этого метода интерпретатор даже не дойдет

        // При этом AuthMiddleware привязан сразу два раза - через контроллер и через маршрут - для примера

        return $this->render('index');
    }
}
