<?php

declare(strict_types=1);

namespace Handler;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class MainHandler extends AbstractHandler
{
    /**
     * Главная страница
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        return $this->render('index');
    }
}
