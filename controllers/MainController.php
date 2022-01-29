<?php

namespace Controllers;

use Exception;
use NW\AbstractController;
use NW\Response\Response;
use NW\Response\ResponseException;

class MainController extends AbstractController
{
    /**
     * Главная страница
     *
     * @return Response
     * @throws Exception
     */
    public function index(): Response
    {
        return $this->render('index');
    }

    /**
     * Пример редиректа
     *
     * @return Response
     * @throws ResponseException
     */
    public function redirectExample(): Response
    {
        return $this->redirect('https://www.google.com/');
    }
}
