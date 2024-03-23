<?php

declare(strict_types=1);

namespace Handlers\Cookie;

use NW\AbstractController;
use NW\AppException;
use NW\Request;
use NW\Response;

class CookieAddHandler extends AbstractController
{
    /**
     * Добавляет новые куки
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        $data = $request->getBody();

        $this->container->getCookies()->setCookie((string)$data['name'], (string)$data['value']);

        return $this->redirect('/cookies');
    }
}
