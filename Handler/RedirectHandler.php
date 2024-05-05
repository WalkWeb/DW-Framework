<?php

declare(strict_types=1);

namespace Handler;

use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

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
