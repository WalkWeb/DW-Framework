<?php

declare(strict_types=1);

namespace Handlers\Post;

use NW\AbstractHandler;
use NW\AppException;
use NW\Captcha;
use NW\Request;
use NW\Response;

class PostAddHandler extends AbstractHandler
{
    /**
     * Отображает форму для создания нового поста
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        $capthca = new Captcha();

        return $this->render('post/add', [
            'captcha' => $capthca->getCaptchaImage(),
        ]);
    }
}
