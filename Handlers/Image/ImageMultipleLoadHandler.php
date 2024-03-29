<?php

declare(strict_types=1);

namespace Handlers\Image;

use Exception;
use NW\AbstractHandler;
use NW\AppException;
use NW\Loader\LoaderImage;
use NW\Request;
use NW\Response;

class ImageMultipleLoadHandler extends AbstractHandler
{
    /**
     * Загрузка нескольких картинок
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        $loader = new LoaderImage($this->container);

        try {
            return $this->render('image/index', ['images' => $loader->multipleLoad($request->getFiles())]);
        } catch (Exception $e) {
            return $this->render('image/index', ['error' => $e->getMessage()]);
        }
    }
}
