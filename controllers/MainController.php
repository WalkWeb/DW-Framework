<?php

namespace Controllers;

use NW\AbstractController;
use NW\Response\Response;

class MainController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('index');
    }
}
