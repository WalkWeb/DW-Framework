<?php

declare(strict_types=1);

namespace Handler\Image;

use Exception;
use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Loader\ImageCollection;
use WalkWeb\NW\Loader\LoaderImage;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

class ImageLoadHandler extends AbstractHandler
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
