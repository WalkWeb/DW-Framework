<?php

declare(strict_types=1);

namespace Handlers\Image;

use Exception;
use NW\AbstractController;
use NW\AppException;
use NW\Loader\ImageCollection;
use NW\Loader\LoaderImage;
use NW\Request;
use NW\Response;

class ImageLoadHandler extends AbstractController
{
    /**
     * Загрузка одной картинки
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        $loader = new LoaderImage($this->container);

        try {
            $loadImages = new ImageCollection();
            $loadImages->add($loader->load($request->getFiles()));
            return $this->render('image/index', ['images' => $loadImages]);
        } catch (Exception $e) {
            return $this->render('image/index', ['error' => $e->getMessage()]);
        }
    }
}
