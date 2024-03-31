<?php

declare(strict_types=1);

namespace Models\User;

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
        $this->container->getConnection()->query(
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
        $data = $this->container->getConnection()->query(
            'SELECT `id`, `login`, `password`, `email`, `auth_token`, `verified_token`, `created_at`, `reg_complete`, `email_verified` 
                FROM `users` WHERE auth_token = ?',
            [['type' => 's', 'value' => $authToken]],
            true
        );

        if (!$data) {
            return null;
        }

        return UserFactory::createFromDB($data);
    }
}
