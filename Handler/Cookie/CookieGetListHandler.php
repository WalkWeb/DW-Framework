<?php

declare(strict_types=1);

namespace Handler\Cookie;

use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

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
