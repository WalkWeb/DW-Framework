<?php

declare(strict_types=1);

namespace Handlers\Post;

use Models\Post\PostException;
use Models\Post\PostDataProvider;
use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class PostGetHandler extends AbstractHandler
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
