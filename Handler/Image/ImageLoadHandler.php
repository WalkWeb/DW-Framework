<?php

declare(strict_types=1);

namespace Handler\Image;

use Exception;
use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Loader\ImageCollection;
use WalkWeb\NW\Loader\LoaderImage;
use WalkWeb\NW\Loader\SimpleImageResizer;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

class ImageLoadHandler extends AbstractHandler
{
    private const MAX_WIDTH  = 500;
    private const MAX_HEIGHT = 500;

    /**
     * Загрузка одной картинки
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            $loader = new LoaderImage($this->container);

            $loadImages = new ImageCollection();
            $image = $loader->load($request->getFiles());
            $loadImages->add($image);

            $resizeImages = [];
            foreach ($loadImages as $loadImage) {
                $resizeImages[] = SimpleImageResizer::resize($loadImage, self::MAX_WIDTH, self::MAX_HEIGHT);
            }

            return $this->render('image/index', [
                'images'       => $loadImages,
                'resizeImages' => $resizeImages,
            ]);

        } catch (Exception $e) {
            return $this->render('image/index', ['error' => $e->getMessage()]);
        }
    }
}
