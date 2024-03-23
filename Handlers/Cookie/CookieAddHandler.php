<?php

declare(strict_types=1);

namespace Handlers\Cookie;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class CookieAddHandler extends AbstractHandler
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
