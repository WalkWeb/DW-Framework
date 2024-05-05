<?php

declare(strict_types=1);

namespace Handler\Post;

use Exception;
use WalkWeb\NW\AbstractHandler;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Captcha;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use WalkWeb\NW\Traits\StringTrait;

class PostAddHandler extends AbstractHandler
{
    use StringTrait;

    /**
     * Отображает форму для создания нового поста
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            $capthca = new Captcha($this->getContainer());

            return $this->render('post/add', [
                'captcha'   => $capthca->getCaptchaImage(),
                'csrfToken' => $this->container->getCsrf()->getCsrfToken(),
            ]);
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }
    }
}
