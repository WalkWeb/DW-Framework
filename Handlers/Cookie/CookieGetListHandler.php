<?php

declare(strict_types=1);

namespace Handlers\Cookie;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class CookieGetListHandler extends AbstractHandler
{
    /**
     * Отображает текущие куки
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        return $this->render(
            'cookies/index',
            ['cookies' => $this->container->getRequest()->getCookies()->getArray()]
        );
    }
}
