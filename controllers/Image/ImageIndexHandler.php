<?php

declare(strict_types=1);

namespace Controllers\Image;

use NW\AbstractController;
use NW\AppException;
use NW\Response;

class ImageIndexHandler extends AbstractController
{
    /**
     * Страница с двумя формами загрузки картинок
     *
     * @return Response
     * @throws AppException
     */
    public function __invoke(): Response
    {
        return $this->render('image/index');
    }
}
