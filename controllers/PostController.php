<?php

namespace Controllers;

use Models\Exceptions\PostException;
use NW\Controller;
use NW\Request\Request;
use NW\Response\Response;
use Models\PostDataProvider;
use Models\Post;

class PostController extends Controller
{
    public function index(): Response
    {
        return $this->render('post/index', [
            'posts' => PostDataProvider::getAllPosts(),
        ]);
    }

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

    public function add(): Response
    {
        return $this->render('post/add');
    }

    public function create(Request $request): Response
    {
        // Исходим из того, что тому, кто захочет посмотреть работу фреймворка будет лень подключать базу,
        // соответственно функционал создания поста не делаем, а просто отображаем отправленные данные

        // Но, показать работу валидатора можно - смотрите её внутри Post
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
            ]);
        }
    }
}
