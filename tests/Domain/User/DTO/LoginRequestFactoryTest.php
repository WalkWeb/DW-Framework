<?php

declare(strict_types=1);

namespace Tests\Domain\User\DTO;

use Exception;
use Domain\User\DTO\LoginRequestFactory;
use Domain\User\UserException;
use Domain\User\UserInterface;
use WalkWeb\NW\AppException;
use Tests\AbstractTest;

class LoginRequestFactoryTest extends AbstractTest
{
    /**
     * Тест на успешное создание объекта LoginRequest на основе данных из формы
     *
     * @dataProvider successDataProvider
     * @param array $data
     * @throws AppException
     */
    public function testLoginRequestFactoryCreateSuccess(array $data): void
    {
        $loginRequest = LoginRequestFactory::create($data);

        self::assertEquals($data['login'], $loginRequest->getLogin());
        self::assertEquals($data['password'], $loginRequest->getPassword());
    }

    /**
     * Тесты на различные варианты невалидных данных
     *
     * @dataProvider failDataProvider
     * @param array $data
     * @param string $error
     * @throws AppException
     */
    public function testLoginRequestFactoryCreateFail(array $data, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        LoginRequestFactory::create($data);
    }

    /**
     * @return array
     */
    public function successDataProvider(): array
    {
        return [
            [
                [
                    'login'    => 'Login',
                    'password' => '12345',
                ],
            ],
        ];
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function failDataProvider(): array
    {
        return [
            [
                // Отсутствует login
                [
                    'password' => 'pass1',
                ],
                UserException::INVALID_LOGIN,
            ],
            [
                // login некорректного типа
                [
                    'login'    => false,
                    'password' => 'pass1',
                ],
                UserException::INVALID_LOGIN,
            ],
            [
                // login меньше минимальный длинны
                [
                    'login'    => self::generateString(UserInterface::LOGIN_MIN_LENGTH - 1),
                    'password' => 'pass1',
                ],
                UserException::INVALID_LOGIN_LENGTH . UserInterface::LOGIN_MIN_LENGTH . '-' . UserInterface::LOGIN_MAX_LENGTH,
            ],
            [
                // login больше максимальной длинны
                [
                    'login'    => self::generateString(UserInterface::LOGIN_MAX_LENGTH + 1),
                    'password' => 'pass1',
                ],
                UserException::INVALID_LOGIN_LENGTH . UserInterface::LOGIN_MIN_LENGTH . '-' . UserInterface::LOGIN_MAX_LENGTH,
            ],
            [
                // login содержит недопустимые символы
                [
                    'login'    => 'User-1$',
                    'password' => 'pass1',
                ],
                UserException::INVALID_LOGIN_SYMBOL,
            ],
            [
                // Отсутствует password
                [
                    'login' => 'User-1',
                ],
                UserException::INVALID_PASSWORD,
            ],
            [
                // password некорректного типа
                [
                    'login'    => 'User-1',
                    'password' => null,
                ],
                UserException::INVALID_PASSWORD,
            ],
            [
                // password меньше минимальной длинны
                [
                    'login'    => 'User-1',
                    'password' => self::generateString(UserInterface::PASSWORD_MIN_LENGTH - 1),
                ],
                UserException::INVALID_PASSWORD_LENGTH . UserInterface::PASSWORD_MIN_LENGTH . '-' . UserInterface::PASSWORD_MAX_LENGTH,
            ],
            [
                // password больше максимальной длинны
                [
                    'login'    => 'User-1',
                    'password' => self::generateString(UserInterface::PASSWORD_MAX_LENGTH + 1),
                ],
                UserException::INVALID_PASSWORD_LENGTH . UserInterface::PASSWORD_MIN_LENGTH . '-' . UserInterface::PASSWORD_MAX_LENGTH,
            ],
        ];
    }
}
