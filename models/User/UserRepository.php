<?php

declare(strict_types=1);

namespace Models\User;

use Models\User\DTO\LoginRequest;
use NW\AppException;
use NW\Container;

class UserRepository
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param UserInterface $user
     * @throws AppException
     */
    public function add(UserInterface $user): void
    {
        if ($this->existUserByLogin($user->getLogin())) {
            throw new AppException(UserException::LOGIN_ALREADY_EXIST);
        }

        if ($this->existUserByEmail($user->getEmail())) {
            throw new AppException(UserException::EMAIL_ALREADY_EXIST);
        }

        $this->container->getConnectionPool()->getConnection()->query(
            'INSERT INTO `users` (`id`, `login`, `password`, `email`, `auth_token`, `verified_token`) VALUES (?, ?, ?, ?, ?, ?)',
            [
                ['type' => 's', 'value' => $user->getId()],
                ['type' => 's', 'value' => $user->getLogin()],
                ['type' => 's', 'value' => $user->getPassword()],
                ['type' => 's', 'value' => $user->getEmail()],
                ['type' => 's', 'value' => $user->getAuthToken()],
                ['type' => 's', 'value' => $user->getVerifiedToken()],
            ],
        );
    }

    /**
     * @param string $authToken
     * @return UserInterface|null
     * @throws AppException
     */
    public function get(string $authToken): ?UserInterface
    {
        $data = $this->container->getConnectionPool()->getConnection()->query(
            'SELECT `id`, `login`, `password`, `email`, `auth_token`, `verified_token`, `created_at`, `reg_complete`, `email_verified`, `template` 
                FROM `users` WHERE auth_token = ?',
            [['type' => 's', 'value' => $authToken]],
            true
        );

        if (!$data) {
            return null;
        }

        return UserFactory::createFromDB($data);
    }

    /**
     * Проверяет, существует ли пользователь с указанным логином и паролем, и если есть - возвращает его авторизационный
     * токен
     *
     * @param LoginRequest $request
     * @param string $hashKey
     * @return string|null
     * @throws AppException
     */
    public function auth(LoginRequest $request, string $hashKey): ?string
    {
        $data = $this->container->getConnectionPool()->getConnection()->query(
            'SELECT `auth_token`, `password` FROM `users` WHERE `login` = ?',
            [
                ['type' => 's', 'value' => $request->getLogin()],
            ],
            true
        );

        if (
            $data &&
            array_key_exists('password', $data) &&
            array_key_exists('auth_token', $data) &&
            password_verify($request->getPassword() . $hashKey, $data['password'])
        ) {
            return $data['auth_token'];
        }

        return null;
    }

    /**
     * @param UserInterface $user
     * @throws AppException
     */
    public function saveTemplate(UserInterface $user): void
    {
        $this->container->getConnectionPool()->getConnection()->query(
            'UPDATE `users` SET `template` = ? WHERE `id` = ?',
            [
                ['type' => 's', 'value' => $user->getTemplate()],
                ['type' => 's', 'value' => $user->getId()],
            ],
        );
    }

    /**
     * @param UserInterface $user
     * @throws AppException
     */
    public function saveVerified(UserInterface $user): void
    {
        $this->container->getConnectionPool()->getConnection()->query(
            'UPDATE `users` SET `email_verified` = ?, `reg_complete` = ? WHERE `id` = ?',
            [
                ['type' => 'i', 'value' => (int)$user->isEmailVerified()],
                ['type' => 'i', 'value' => (int)$user->isRegComplete()],
                ['type' => 's', 'value' => $user->getId()],
            ],
        );
    }

    /**
     * @param string $login
     * @return bool
     * @throws AppException
     */
    private function existUserByLogin(string $login): bool
    {
        $data = $this->container->getConnectionPool()->getConnection()->query(
            'SELECT * FROM `users` WHERE `login` = ?',
            [['type' => 's', 'value' => $login]],
        );

        return count($data) > 0;
    }

    /**
     * @param string $email
     * @return bool
     * @throws AppException
     */
    private function existUserByEmail(string $email): bool
    {
        $data = $this->container->getConnectionPool()->getConnection()->query(
            'SELECT * FROM `users` WHERE `email` = ?',
            [['type' => 's', 'value' => $email]],
        );

        return count($data) > 0;
    }
}
