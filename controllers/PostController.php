<?php

namespace Controllers;

use Models\Exceptions\PostException;
use NW\Controller;
use NW\Request\Request;
use NW\Response\Response;
use Models\PostDataProvider;

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
}
