<?php

namespace Controllers;

use Exception;
use Models\Exceptions\PostException;
use NW\Controller;
use NW\Pagination;
use NW\Request\Request;
use NW\Response\Response;
use Models\PostDataProvider;
use Models\Post;
use NW\Captcha;

class PostController extends Controller
{
    /**
     * Отображает список постов
     *
     * По умолчанию отображается первая страница списка, если в URL указана страница - то будут отображены посты для
     * соответствующей страницы.
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request): Response
    {
        try {
            $posts = PostDataProvider::getPacksPost($request->page);

            return $this->render('post/index', [
                'posts'      => $posts,
                'pagination' => Pagination::getPages(PostDataProvider::getPostsCount(), $request->page, '/posts/'),
            ]);

        } catch (PostException $e) {
            return $this->renderErrorPage();
        }
    }

    /**
     * Отображает пост по указанному ID
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function view(Request $request): Response
    {
        try {
            return $this->render('post/view', [
                'post' => PostDataProvider::getPostById($request->id),
            ]);
        } catch (PostException $e) {
            return $this->renderErrorPage($e->getMessage());
        }
    }

    /**
     * Отображает форму для создания нового поста
     *
     * @return Response
     * @throws Exception
     */
    public function add(): Response
    {
        return $this->render('post/add', [
            'captcha' => Captcha::getCaptchaImage(),
        ]);
    }

    /**
     * Обрабатывает данные для создания нового поста
     *
     * Исходим из того, что тому, кто захочет посмотреть работу фреймворка будет лень подключать базу,
     * соответственно функционал создания поста не делаем, а просто валидируем и отображаем отправленные данные.
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(Request $request): Response
    {
        if (!Captcha::checkCaptcha($request->captcha)) {
            return $this->render('post/add', [
                'message' => Captcha::INVALID_CAPTCHA,
                'title'   => $request->title,
                'text'    => $request->text,
                'captcha' => Captcha::getCaptchaImage(),
            ]);
        }

        try {
            $post = new Post($request->title, $request->text);

            return $this->render('post/create', [
                'post' => $post,
            ]);

        } catch (PostException $e) {
            $message = $e->getMessage();
            return $this->render('post/add', [
                'message' => $message,
                'title'   => $request->title,
                'text'    => $request->text,
                'captcha' => Captcha::getCaptchaImage(),
            ]);
        }
    }
}
