<?php

declare(strict_types=1);

namespace Handlers;

use NW\AbstractController;
use NW\AppException;
use NW\Response;

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
