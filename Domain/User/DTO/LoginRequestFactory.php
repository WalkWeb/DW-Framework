<?php

declare(strict_types=1);

namespace Domain\User\DTO;

use Domain\User\UserException;
use Domain\User\UserInterface;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Traits\ValidationTrait;

class LoginRequestFactory
{
    use ValidationTrait;

    /**
     * @param array $data
     * @return LoginRequest
     * @throws AppException
     */
    public static function create(array $data): LoginRequest
    {
        return new LoginRequest(
            self::loginValidation($data),
            self::passwordValidate($data),
        );
    }

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    private static function loginValidation(array $data): string
    {
        $login = self::string($data, 'login', UserException::INVALID_LOGIN);

        self::stringMinMaxLength(
            $login,
            UserInterface::LOGIN_MIN_LENGTH,
            UserInterface::LOGIN_MAX_LENGTH,
            UserException::INVALID_LOGIN_LENGTH . UserInterface::LOGIN_MIN_LENGTH . '-' . UserInterface::LOGIN_MAX_LENGTH
        );

        self::parent($login, '/^[a-zA-Z0-9а-яА-ЯёЁ\-_]*$/u', UserException::INVALID_LOGIN_SYMBOL);

        return $login;
    }

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    private static function passwordValidate(array $data): string
    {
        $password = self::string($data, 'password', UserException::INVALID_PASSWORD);

        self::stringMinMaxLength(
            $password,
            UserInterface::PASSWORD_MIN_LENGTH,
            UserInterface::PASSWORD_MAX_LENGTH,
            UserException::INVALID_PASSWORD_LENGTH . UserInterface::PASSWORD_MIN_LENGTH . '-' . UserInterface::PASSWORD_MAX_LENGTH
        );

        return $password;
    }
}
