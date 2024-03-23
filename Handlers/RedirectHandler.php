<?php

declare(strict_types=1);

namespace Handlers;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class RedirectHandler extends AbstractHandler
{
    /**
     * Пример редиректа
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        return $this->redirect('https://www.google.com/');
    }
}
