<?php

declare(strict_types=1);

namespace Handler\Post;

use Domain\Post\PostException;
use Domain\Post\PostRepository;
use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;
use NW\Traits\PaginationTrait;

class PostGetListHandler extends AbstractHandler
{
    use PaginationTrait;

    /**
     * Отображает список постов
     *
     * По умолчанию отображается первая страница списка, если в URL указана страница - то будут отображены посты для
     * соответствующей страницы.
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            $repository = new PostRepository($this->container);

            $page = $request->page;
            $perPage = 5;
            $offset = ($page - 1) * $perPage;
            $total = $repository->getTotalCount();

            if ($page > ceil($total / $perPage)) {
                throw new PostException(PostException::NOT_FOUND, Response::NOT_FOUND);
            }

            $posts = $repository->getList($offset, $perPage);

            return $this->render('post/index', [
                'posts'      => $posts,
                'pagination' => $this->getPages($total, $page, '/posts/', $perPage),
            ]);

        } catch (PostException $e) {
            return $this->renderErrorPage();
        }
    }
}
