<?php

namespace Controllers;

use NW\Controller;
use NW\Response\Response;
use Models\PostDataProvider;

class PostController extends Controller
{
    public function index(): Response
    {
        return $this->render('posts', [
            'posts' => PostDataProvider::getAllPosts(),
        ]);
    }
}
