<?php

declare(strict_types=1);

namespace Handler\User;

use Domain\User\UserFactory;
use Domain\User\UserInterface;
use Domain\User\UserRepository;
use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;

class UserCreateHandler extends AbstractHandler
{
    /**
     * Создание нового пользователя на основе данных из формы регистрации
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            $user = UserFactory::createNew($request->getBody(), KEY, TEMPLATE_DEFAULT);
            $repository = new UserRepository($this->container);
            $repository->add($user);
            $this->container->getCookies()->set(UserInterface::AUTH_TOKEN, $user->getAuthToken());

            $appName = APP_NAME;
            $url = HOST . 'check_email/' . $user->getVerifiedToken();

            $this->container->getMailer()->send(
                $user->getEmail(),
                "Подтверждение регистрации на $appName",
                "<p>Кто-то (возможно, вы) зарегистрировался на $appName, если это были вы - для завершения регистрации перейдите 
                        по ссылке <a href='$url'>$url</a></p>
                        <p>Если вы не регистрировались на $appName, то просто проигнорируйте это письмо.</p>
                        <p>В любом случае не передавайте третьим лицам ссылку из письма.</p>",
            );

            return $this->redirect('/verified_email');

        } catch (AppException $e) {
            return $this->render('user/registration', ['error' => $e->getMessage()]);
        }
    }
}
