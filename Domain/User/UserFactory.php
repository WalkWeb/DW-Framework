<?php

declare(strict_types=1);

namespace Domain\User;

use DateTime;
use Exception;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Traits\StringTrait;
use WalkWeb\NW\Traits\ValidationTrait;
use Ramsey\Uuid\Uuid;

class UserFactory
{
    use ValidationTrait;
    use StringTrait;

    /**
     * Создает объект User на основе данных из формы регистрации
     *
     * @param array $data
     * @param string $hashKey
     * @param string $defaultTemplate
     * @return UserInterface
     * @throws AppException
     */
    public static function createNew(array $data, string $hashKey, string $defaultTemplate): UserInterface
    {
        try {
            $password = self::passwordValidate($data);
            $password = password_hash($password . $hashKey, PASSWORD_BCRYPT, ['cost' => 10]);

            return new User(
                Uuid::uuid4()->toString(),
                self::loginValidation($data),
                $password,
                self::emailValidate($data),
                false,
                false,
                self::generateString(30),
                self::generateString(30),
                $defaultTemplate,
                new DateTime()
            );
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * Создает объект User на основе данных из базы
     *
     * @param array $data
     * @return UserInterface
     * @throws AppException
     */
    public static function createFromDB(array $data): UserInterface
    {
        $id = self::string($data, 'id', UserException::INVALID_ID);
        $createdAt = self::string($data, 'created_at', UserException::INVALID_CREATED_AT);

        return new User(
            self::uuid($id, UserException::INVALID_ID_VALUE),
            self::loginValidation($data),
            self::passwordValidate($data),
            self::emailValidate($data),
            (bool)self::int($data, 'reg_complete', UserException::INVALID_REG_COMPLETE),
            (bool)self::int($data, 'email_verified', UserException::INVALID_EMAIL_VERIFIED),
            self::authTokenValidate($data),
            self::verifiedTokenValidate($data),
            self::templateValidate($data),
            self::date($createdAt, UserException::INVALID_CREATED_AT_VALUE),
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

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    private static function emailValidate(array $data): string
    {
        $email = self::string($data, 'email', UserException::INVALID_EMAIL);

        self::stringMinMaxLength(
            $email,
            UserInterface::EMAIL_MIN_LENGTH,
            UserInterface::EMAIL_MAX_LENGTH,
            UserException::INVALID_EMAIL_LENGTH . UserInterface::EMAIL_MIN_LENGTH . '-' . UserInterface::EMAIL_MAX_LENGTH
        );

        self::email($email, UserException::INVALID_EMAIL_SYMBOL);

        return $email;
    }

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    private static function authTokenValidate(array $data): string
    {
        $authToken = self::string($data, 'auth_token', UserException::INVALID_AUTH_TOKEN);

        self::stringMinMaxLength(
            $authToken,
            UserInterface::AUTH_TOKEN_MIN_LENGTH,
            UserInterface::AUTH_TOKEN_MAX_LENGTH,
            UserException::INVALID_AUTH_TOKEN_LENGTH . UserInterface::AUTH_TOKEN_MIN_LENGTH . '-' . UserInterface::AUTH_TOKEN_MAX_LENGTH
        );

        return $authToken;
    }

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    private static function verifiedTokenValidate(array $data): string
    {
        $verifiedToken = self::string($data, 'verified_token', UserException::INVALID_VERIFIED_TOKEN);

        self::stringMinMaxLength(
            $verifiedToken,
            UserInterface::AUTH_TOKEN_MIN_LENGTH,
            UserInterface::AUTH_TOKEN_MAX_LENGTH,
            UserException::INVALID_VERIFIED_TOKEN_LENGTH . UserInterface::AUTH_TOKEN_MIN_LENGTH . '-' . UserInterface::AUTH_TOKEN_MAX_LENGTH
        );

        return $verifiedToken;
    }

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    private static function templateValidate(array $data): string
    {
        $template = self::string($data, 'template', UserException::INVALID_TEMPLATE);

        self::stringMinMaxLength(
            $template,
            UserInterface::TEMPLATE_MIN_LENGTH,
            UserInterface::TEMPLATE_MAX_LENGTH,
            UserException::INVALID_TEMPLATE_LENGTH . UserInterface::TEMPLATE_MIN_LENGTH . '-' . UserInterface::TEMPLATE_MAX_LENGTH
        );

        return $template;
    }
}
