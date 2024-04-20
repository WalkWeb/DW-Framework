<?php

declare(strict_types=1);

namespace Handler\User;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class VerifiedEmailHandler extends AbstractHandler
{
    /**
     * Отображает страницу с информацией о необходимости подтвердить email
     *
     * Если страницу пытается открыть неавторизованный пользователь - ошибка "вы не авторизованны"
     *
     * Если пользователь уже имеет подтвержденный email - сообщение о том, что все ок, и больше ничего делать не нужно
     *
     * Если пользователь зарегистрирован, но не подтвердил почту - сообщение о необходимости подтверждения
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        if (!$this->container->exist('user')) {
            $this->title = 'Необходима авторизация';
            $message = '<p>Необходима авторизация</p>
                        <p><a href="/login">Перейти на страницу авторизации</a></p>';

            return $this->render('user/email_verified', ['message' => $message]);
        }

        if ($this->container->getUser()->isEmailVerified()) {
            $this->title = 'Email успешно подтвержден';
            $message = '<p>Вы успешно подтвердили email, ваш аккаунт активирован и все возможности доступны.</p>
                        <p><a href="/">Перейти на главную</a></p>';

            return $this->render('user/email_verified', ['message' => $message]);
        }

        $this->title = 'Подтвердите ваш email';
        $message = '<p>Вам необходимо подтвердить ваш email. Инструкция по активации отправлена на email указанный при регистрации.</p>
                    <p>Если вам не пришло письмо – свяжитесь с нашей службой поддержки.</p>';

        return $this->render('user/email_verified', ['message' => $message]);
    }
}
