<?php

declare(strict_types=1);

namespace Controllers;

use Exception;
use NW\AbstractController;
use NW\Request;
use NW\Response\Response;

class CookieController extends AbstractController
{
    /**
     * @return Response
     * @throws Exception
     */
    public function index(): Response
    {
        return $this->render('cookies/index', ['cookies' => $this->container->getRequest()->getCookies()->getCookies()]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function add(Request $request): Response
    {
        $data = $request->getBody();

        $this->container->getCookies()->setCookie((string)$data['name'], (string)$data['value']);

        return $this->redirect('/cookies');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $data = $request->getBody();

        $this->container->getCookies()->deleteCookie((string)$data['name']);

        return $this->redirect('/cookies');
    }
}
