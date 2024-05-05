<?php

declare(strict_types=1);

namespace Handler\Image;

use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

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
