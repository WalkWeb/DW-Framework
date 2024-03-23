<?php

declare(strict_types=1);

namespace Handlers\Post;

use NW\AbstractController;
use NW\AppException;
use NW\Captcha;
use NW\Response;

class PostAddHandler extends AbstractController
{
    /**
     * Отображает форму для создания нового поста
     *
     * @return Response
     * @throws AppException
     */
    public function __invoke(): Response
    {
        $capthca = new Captcha();

        return $this->render('post/add', [
            'captcha' => $capthca->getCaptchaImage(),
        ]);
    }
}
