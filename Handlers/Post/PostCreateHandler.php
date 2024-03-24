<?php

declare(strict_types=1);

namespace Handlers\Post;

use Models\Post\Post;
use NW\AbstractHandler;
use NW\AppException;
use NW\Captcha;
use NW\Request;
use NW\Response;
use Ramsey\Uuid\Uuid;

class PostCreateHandler extends AbstractHandler
{
    /**
     * Обрабатывает запрос на создание нового поста
     *
     * Исходим из того, что тому, кто захочет посмотреть работу фреймворка будет лень подключать базу,
     * соответственно функционал создания поста не делаем, а просто валидируем и отображаем отправленные данные.
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        $capthca = $this->container->getCaptcha();

        if (!$capthca->checkCaptcha($request->captcha)) {
            return $this->render('post/add', [
                'message' => Captcha::INVALID_CAPTCHA,
                'title'   => $request->title,
                'text'    => $request->text,
                'captcha' => $capthca->getCaptchaImage(),
            ]);
        }

        try {
            $post = new Post($this->container, Uuid::uuid4()->toString(), $request->title, $request->text);

            return $this->render('post/create', [
                'post' => $post,
            ]);

        } catch (AppException $e) {
            $message = $e->getMessage();
            return $this->render('post/add', [
                'message' => $message,
                'title'   => $request->title,
                'text'    => $request->text,
                'captcha' => $capthca->getCaptchaImage(),
            ]);
        }
    }
}
