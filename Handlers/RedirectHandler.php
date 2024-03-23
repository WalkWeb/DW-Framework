<?php

declare(strict_types=1);

namespace Handlers;

use NW\AbstractController;
use NW\AppException;
use NW\Response;

class RedirectHandler extends AbstractController
{
    /**
     * Пример редиректа
     *
     * @return Response
     * @throws AppException
     */
    public function __invoke(): Response
    {
        return $this->redirect('https://www.google.com/');
    }
}
