<?php

declare(strict_types=1);

namespace Controllers\Cookie;

use NW\AbstractController;
use NW\AppException;
use NW\Request;
use NW\Response;

class CookieDeleteHandler extends AbstractController
{
    /**
     * Удаляет указанные куки
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        $data = $request->getBody();

        $this->container->getCookies()->deleteCookie((string)$data['name']);

        return $this->redirect('/cookies');
    }
}
