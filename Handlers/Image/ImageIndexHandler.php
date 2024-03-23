<?php

declare(strict_types=1);

namespace Handlers\Image;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class ImageIndexHandler extends AbstractHandler
{
    /**
     * Страница с двумя формами загрузки картинок
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        return $this->render('image/index');
    }
}
