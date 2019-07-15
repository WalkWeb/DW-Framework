<?php

namespace Controllers;

use Models\Exceptions\PostException;
use NW\Controller;
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
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('post/index', [
            'posts' => PostDataProvider::getAllPosts(),
        ]);
    }

    /**
     * Отображает пост по указанному ID
     *
     * @param Request $request
     * @return Response
     */
    public function view(Request $request): Response
    {
        try {
            return $this->render('post/view', [
                'post' => PostDataProvider::getPostById($request->getAttribute('id')),
            ]);
        } catch (PostException $e) {
            return $this->pageNotFound($e->getMessage());
        }
    }

    /**
     * Отображает форму для создания нового поста
     *
     * @return Response
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
     */
    public function create(Request $request): Response
    {
        if (!Captcha::checkCaptcha($request->getBody()['captcha'])) {
            return $this->render('post/add', [
                'message' => Captcha::INVALID_CAPTCHA,
                'title' => $request->getBody()['title'],
                'text' => $request->getBody()['text'],
                'captcha' => Captcha::getCaptchaImage(),
            ]);
        }

        try {
            $post = new Post($request->getBody()['title'], $request->getBody()['text']);

            return $this->render('post/create', [
                'post' => $post,
            ]);

        } catch (PostException $e) {
            $message = $e->getMessage();
            return $this->render('post/add', [
                'message' => $message,
                'title' => $request->getBody()['title'],
                'text' => $request->getBody()['text'],
                'captcha' => Captcha::getCaptchaImage(),
            ]);
        }
    }
}
