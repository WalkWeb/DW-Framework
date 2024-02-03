<?php

namespace Controllers;

use Exception;
use NW\AbstractController;
use NW\AppException;
use NW\Response;

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
     * @throws AppException
     */
    public function redirectExample(): Response
    {
        return $this->redirect('https://www.google.com/');
    }
}
