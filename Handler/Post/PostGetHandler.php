<?php

declare(strict_types=1);

namespace Handler\Post;

use Domain\Post\PostException;
use Domain\Post\PostRepository;
use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

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
            $repository = new PostRepository($this->container);

            return $this->render('post/view', [
                'post' => $repository->get($request->id),
            ]);
        } catch (PostException $e) {
            return $this->renderErrorPage($e->getMessage());
        }
    }
}
