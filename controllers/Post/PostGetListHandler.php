<?php

declare(strict_types=1);

namespace Controllers\Post;

use Models\Exceptions\PostException;
use Models\PostDataProvider;
use NW\AbstractController;
use NW\AppException;
use NW\Request;
use NW\Response;
use NW\Traits\PaginationTrait;

class PostGetListHandler extends AbstractController
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
            $posts = PostDataProvider::getPacksPost($request->page);

            return $this->render('post/index', [
                'posts'      => $posts,
                'pagination' => $this->getPages(PostDataProvider::getPostsCount(), $request->page, '/posts/'),
            ]);

        } catch (PostException $e) {
            return $this->renderErrorPage();
        }
    }
}
