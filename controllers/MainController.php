<?php

namespace Controllers;

use NW\Controller;
use NW\Response\Response;

class MainController extends Controller
{
    public function index(): Response
    {
        return $this->render('index');
    }
}
