<?php

declare(strict_types=1);

namespace Controllers\Cookie;

use NW\AbstractController;
use NW\AppException;
use NW\Response;

class CookieGetListHandler extends AbstractController
{
    /**
     * Отображает текущие куки
     *
     * @return Response
     * @throws AppException
     */
    public function __invoke(): Response
    {
        return $this->render(
            'cookies/index',
            ['cookies' => $this->container->getRequest()->getCookies()->getCookies()]
        );
    }
}
