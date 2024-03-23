<?php

declare(strict_types=1);

namespace Controllers;

use NW\AbstractController;
use NW\AppException;
use NW\Response;

// TODO Заменить неймспейс и директорию на хандлеры

class MainHandler extends AbstractController
{
    /**
     * Главная страница
     *
     * @return Response
     * @throws AppException
     */
    public function __invoke(): Response
    {
        return $this->render('index');
    }
}
