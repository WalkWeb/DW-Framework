<?php

declare(strict_types=1);

namespace Controllers;

use NW\AbstractController;
use NW\AppException;
use NW\Loader\LoaderException;
use NW\Loader\LoaderImage;
use NW\Request;
use NW\Response;

class ImageController extends AbstractController
{
    /**
     * @return Response
     * @throws AppException
     */
    public function index(): Response
    {
        return $this->render('image/index');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function load(Request $request): Response
    {
        $loader = new LoaderImage($this->container);

        try {
            return $this->render('image/index', ['image' => $loader->load($request->getFiles())]);
        } catch (LoaderException $e) {
            return $this->render('image/index', ['error' => $e->getMessage()]);
        }
    }
}
