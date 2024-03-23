<?php

declare(strict_types=1);

namespace Controllers\Post;

use Models\Exceptions\PostException;
use Models\PostDataProvider;
use NW\AbstractController;
use NW\AppException;
use NW\Request;
use NW\Response;

class PostGetHandler extends AbstractController
{
    /**
     * Отображает пост по указанному ID
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            return $this->render('post/view', [
                'post' => PostDataProvider::getPostById($request->id),
            ]);
        } catch (PostException $e) {
            return $this->renderErrorPage($e->getMessage());
        }
    }
}
