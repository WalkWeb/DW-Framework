<?php

declare(strict_types=1);

namespace Handler\Post;

use Exception;
use NW\AbstractHandler;
use NW\AppException;
use NW\Captcha;
use NW\Request;
use NW\Response;
use NW\Traits\StringTrait;

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
