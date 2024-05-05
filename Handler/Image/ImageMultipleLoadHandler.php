<?php

declare(strict_types=1);

namespace Handler\Image;

use Exception;
use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Loader\LoaderImage;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

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
