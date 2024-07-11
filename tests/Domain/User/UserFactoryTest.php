<?php

declare(strict_types=1);

namespace Tests\Domain\User;

use DateTime;
use Exception;
use Domain\User\UserException;
use Domain\User\UserFactory;
use Domain\User\UserInterface;
use WalkWeb\NW\AppException;
use Ramsey\Uuid\Uuid;
use Tests\AbstractTest;

class UserFactoryTest extends AbstractTest
{
    /**
     * Тест на успешное создание объекта User на основе данных из формы регистрации
     *
     * @dataProvider createNewSuccessDataProvider
     * @param array $data
     * @throws AppException
     */
    public function testUserFactoryCreateNewSuccess(array $data): void
    {
        $templateDefault = 'template';
        $user = UserFactory::createNew($data, 'pass_key', $templateDefault);

        self::assertTrue(Uuid::isValid($user->getId()));
        self::assertEquals($data['login'], $user->getLogin());
        self::assertEquals(60, strlen($user->getPassword()));
        self::assertEquals($data['email'], $user->getEmail());
        self::assertFalse($user->isRegComplete());
        self::assertFalse($user->isEmailVerified());
        self::assertEquals(30, strlen($user->getAuthToken()));
        self::assertEquals(30, strlen($user->getVerifiedToken()));
        self::assertEquals($templateDefault, $user->getTemplate());
        self::assertEquals(
            (new DateTime())->format('Y-m-d H:i:s'),
            $user->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }

    /**
     * Тест на различные варианты невалидных данных из формы регистрации
     *
     * @dataProvider createNewFailDataProvider
     * @param array $data
     * @param string $error
     * @throws AppException
     */
    public function testUserFactoryCreateNewFail(array $data, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        UserFactory::createNew($data, 'hash_key', 'default');
    }

    /**
     * Тест на успешное создание объекта User на основе данных из базы
     *
     * @dataProvider createDBSuccessDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testUserFactoryCreateFromDBSuccess(array $data): void
    {
        $user = UserFactory::createFromDB($data);

        self::assertEquals($data['id'], $user->getId());
        self::assertEquals($data['login'], $user->getLogin());
        self::assertEquals($data['password'], $user->getPassword());
        self::assertEquals($data['email'], $user->getEmail());
        self::assertEquals((bool)$data['reg_complete'], $user->isRegComplete());
        self::assertEquals((bool)$data['email_verified'], $user->isEmailVerified());
        self::assertEquals($data['auth_token'], $user->getAuthToken());
        self::assertEquals($data['verified_token'], $user->getVerifiedToken());
        self::assertEquals($data['template'], $user->getTemplate());
        self::assertEquals($data['created_at'], $user->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    /**
     * Тест на различные варианты невалидных данных из базы
     *
     * @dataProvider createDBFailDataProvider
     * @param array $data
     * @param string $error
     * @throws Exception
     */
    public function testUserFactoryCreateFromDBFail(array $data, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        UserFactory::createFromDB($data);
    }

    /**
     * @return array
     */
    public function createNewSuccessDataProvider(): array
    {
        return [
            [
                [
                    'login'    => 'User-1',
                    'password' => 'pass1',
                    'email'    => 'mail@mail.com',
                ],
            ],
        ];
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function createNewFailDataProvider(): array
    {
        return [
            [
                // Отсутствует login
                [
                    'password' => 'pass1',
                    'email'    => 'mail@mail.com',
                ],
                UserException::INVALID_LOGIN,
            ],
            [
                // login некорректного типа
                [
                    'login'    => false,
                    'password' => 'pass1',
                    'email'    => 'mail@mail.com',
                ],
                UserException::INVALID_LOGIN,
            ],
            [
                // login меньше минимальный длинны
                [
                    'login'    => self::generateString(UserInterface::LOGIN_MIN_LENGTH - 1),
                    'password' => 'pass1',
                    'email'    => 'mail@mail.com',
                ],
                UserException::INVALID_LOGIN_LENGTH . UserInterface::LOGIN_MIN_LENGTH . '-' . UserInterface::LOGIN_MAX_LENGTH,
            ],
            [
                // login больше максимальной длинны
                [
                    'login'    => self::generateString(UserInterface::LOGIN_MAX_LENGTH + 1),
                    'password' => 'pass1',
                    'email'    => 'mail@mail.com',
                ],
                UserException::INVALID_LOGIN_LENGTH . UserInterface::LOGIN_MIN_LENGTH . '-' . UserInterface::LOGIN_MAX_LENGTH,
            ],
            [
                // login содержит недопустимые символы
                [
                    'login'    => 'User-1$',
                    'password' => 'pass1',
                    'email'    => 'mail@mail.com',
                ],
                UserException::INVALID_LOGIN_SYMBOL,
            ],
            [
                // Отсутствует password
                [
                    'login' => 'User-1',
                    'email' => 'mail@mail.com',
                ],
                UserException::INVALID_PASSWORD,
            ],
            [
                // password некорректного типа
                [
                    'login'    => 'User-1',
                    'password' => null,
                    'email'    => 'mail@mail.com',
                ],
                UserException::INVALID_PASSWORD,
            ],
            [
                // password меньше минимальной длинны
                [
                    'login'    => 'User-1',
                    'password' => self::generateString(UserInterface::PASSWORD_MIN_LENGTH - 1),
                    'email'    => 'mail@mail.com',
                ],
                UserException::INVALID_PASSWORD_LENGTH . UserInterface::PASSWORD_MIN_LENGTH . '-' . UserInterface::PASSWORD_MAX_LENGTH,
            ],
            [
                // password больше максимальной длинны
                [
                    'login'    => 'User-1',
                    'password' => self::generateString(UserInterface::PASSWORD_MAX_LENGTH + 1),
                    'email'    => 'mail@mail.com',
                ],
                UserException::INVALID_PASSWORD_LENGTH . UserInterface::PASSWORD_MIN_LENGTH . '-' . UserInterface::PASSWORD_MAX_LENGTH,
            ],
            [
                // Отсутствует email
                [
                    'login'    => 'User-1',
                    'password' => 'pass1',
                ],
                UserException::INVALID_EMAIL,
            ],
            [
                // email некорректного типа
                [
                    'login'    => 'User-1',
                    'password' => 'pass1',
                    'email'    => [],
                ],
                UserException::INVALID_EMAIL,
            ],
            [
                // email меньше минимальной длинны
                [
                    'login'    => 'User-1',
                    'password' => 'pass1',
                    'email'    => self::generateString(UserInterface::EMAIL_MIN_LENGTH - 1),
                ],
                UserException::INVALID_EMAIL_LENGTH . UserInterface::EMAIL_MIN_LENGTH . '-' . UserInterface::EMAIL_MAX_LENGTH,
            ],
            [
                // email больше максимальной длинны
                [
                    'login'    => 'User-1',
                    'password' => 'pass1',
                    'email'    => self::generateString(UserInterface::EMAIL_MAX_LENGTH + 1),
                ],
                UserException::INVALID_EMAIL_LENGTH . UserInterface::EMAIL_MIN_LENGTH . '-' . UserInterface::EMAIL_MAX_LENGTH,
            ],
            [
                // невалидный email
                [
                    'login'    => 'User-1',
                    'password' => 'pass1',
                    'email'    => 'invalid_mail@',
                ],
                UserException::INVALID_EMAIL_SYMBOL,
            ],
        ];
    }

    /**
     * @return array
     */
    public function createDBSuccessDataProvider(): array
    {
        return [
            [
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
            ],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function createDBFailDataProvider(): array
    {
        return [
            [
                // Отсутствует id
                [
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_ID,
            ],
            [
                // id некорректного типа
                [
                    'id'             => true,
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_ID,
            ],
            [
                // id невалидный uuid
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de6_',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_ID,
            ],
            [
                // Отсутствует login
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'password'       => 'pass1',
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_LOGIN,
            ],
            [
                // login некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => false,
                    'password'       => 'pass1',
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_LOGIN,
            ],
            [
                // login меньше минимальный длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => self::generateString(UserInterface::LOGIN_MIN_LENGTH - 1),
                    'password'       => 'pass1',
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_LOGIN_LENGTH . UserInterface::LOGIN_MIN_LENGTH . '-' . UserInterface::LOGIN_MAX_LENGTH,
            ],
            [
                // login больше максимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => self::generateString(UserInterface::LOGIN_MAX_LENGTH + 1),
                    'password'       => 'pass1',
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_LOGIN_LENGTH . UserInterface::LOGIN_MIN_LENGTH . '-' . UserInterface::LOGIN_MAX_LENGTH,
            ],
            [
                // login содержит недопустимые символы
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1$',
                    'password'       => 'pass1',
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_LOGIN_SYMBOL,
            ],
            [
                // Отсутствует password
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_PASSWORD,
            ],
            [
                // password некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'password'       => null,
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_PASSWORD,
            ],
            [
                // password меньше минимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'password'       => self::generateString(UserInterface::PASSWORD_MIN_LENGTH - 1),
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_PASSWORD_LENGTH . UserInterface::PASSWORD_MIN_LENGTH . '-' . UserInterface::PASSWORD_MAX_LENGTH,
            ],
            [
                // password больше максимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'password'       => self::generateString(UserInterface::PASSWORD_MAX_LENGTH + 1),
                    'email'          => 'mail@mail.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_PASSWORD_LENGTH . UserInterface::PASSWORD_MIN_LENGTH . '-' . UserInterface::PASSWORD_MAX_LENGTH,
            ],
            [
                // Отсутствует email
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'password'       => 'pass1',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_EMAIL,
            ],
            [
                // email некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'password'       => 'pass1',
                    'email'          => [],
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_EMAIL,
            ],
            [
                // email меньше минимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'password'       => 'pass1',
                    'email'          => self::generateString(UserInterface::EMAIL_MIN_LENGTH - 1),
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_EMAIL_LENGTH . UserInterface::EMAIL_MIN_LENGTH . '-' . UserInterface::EMAIL_MAX_LENGTH,
            ],
            [
                // email больше максимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'password'       => 'pass1',
                    'email'          => self::generateString(UserInterface::EMAIL_MAX_LENGTH + 1),
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_EMAIL_LENGTH . UserInterface::EMAIL_MIN_LENGTH . '-' . UserInterface::EMAIL_MAX_LENGTH,
            ],
            [
                // невалидный email
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'User-1',
                    'password'       => 'pass1',
                    'email'          => 'invalid_mail@',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_EMAIL_SYMBOL,
            ],
            [
                // Отсутствует reg_complete
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_REG_COMPLETE,
            ],
            [
                // reg_complete некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => true,
                    'email_verified' => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_REG_COMPLETE,
            ],
            [
                // Отсутствует email_verified
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 1,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_EMAIL_VERIFIED,
            ],
            [
                // email_verified некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 1,
                    'email_verified' => 'true',
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_EMAIL_VERIFIED,
            ],
            [
                // Отсутствует auth_token
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_AUTH_TOKEN,
            ],
            [
                // auth_token некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 1,
                    'email_verified' => 1,
                    'auth_token'     => 123,
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_AUTH_TOKEN,
            ],
            [
                // auth_token меньше минимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => self::generateString(UserInterface::AUTH_TOKEN_MIN_LENGTH - 1),
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_AUTH_TOKEN_LENGTH . UserInterface::AUTH_TOKEN_MIN_LENGTH . '-' . UserInterface::AUTH_TOKEN_MAX_LENGTH,
            ],
            [
                // auth_token больше максимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => self::generateString(UserInterface::AUTH_TOKEN_MAX_LENGTH + 1),
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_AUTH_TOKEN_LENGTH . UserInterface::AUTH_TOKEN_MIN_LENGTH . '-' . UserInterface::AUTH_TOKEN_MAX_LENGTH,
            ],
            [
                // Отсутствует verified_token
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_VERIFIED_TOKEN,
            ],
            [
                // verified_token некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => false,
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_VERIFIED_TOKEN,
            ],
            [
                // verified_token меньше минимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => self::generateString(UserInterface::VERIFIED_TOKEN_MIN_LENGTH - 1),
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_VERIFIED_TOKEN_LENGTH . UserInterface::AUTH_TOKEN_MIN_LENGTH . '-' . UserInterface::AUTH_TOKEN_MAX_LENGTH,
            ],
            [
                // verified_token больше максимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => self::generateString(UserInterface::VERIFIED_TOKEN_MAX_LENGTH + 1),
                    'template'       => 'default',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_VERIFIED_TOKEN_LENGTH . UserInterface::AUTH_TOKEN_MIN_LENGTH . '-' . UserInterface::AUTH_TOKEN_MAX_LENGTH,
            ],
            [
                // Отсутствует template
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_TEMPLATE,
            ],
            [
                // template некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'template'       => true,
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_TEMPLATE,
            ],
            [
                // template меньше минимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'template'       => self::generateString(UserInterface::TEMPLATE_MIN_LENGTH - 1),
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_TEMPLATE_LENGTH . UserInterface::TEMPLATE_MIN_LENGTH . '-' . UserInterface::TEMPLATE_MAX_LENGTH,
            ],
            [
                // template больше максимальной длинны
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'template'       => self::generateString(UserInterface::TEMPLATE_MAX_LENGTH + 1),
                    'created_at'     => '2024-03-28 12:06:35',
                ],
                UserException::INVALID_TEMPLATE_LENGTH . UserInterface::TEMPLATE_MIN_LENGTH . '-' . UserInterface::TEMPLATE_MAX_LENGTH,
            ],
            [
                // Отсутствует created_at
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                ],
                UserException::INVALID_CREATED_AT,
            ],
            [
                // created_at некорректного типа
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => 1.2,
                ],
                UserException::INVALID_CREATED_AT,
            ],
            [
                // created_at некорректная дата
                [
                    'id'             => 'e19725de-24ad-41d0-a8b1-96016f26de64',
                    'login'          => 'Login-1',
                    'password'       => '12345',
                    'email'          => 'email@email.com',
                    'reg_complete'   => 0,
                    'email_verified' => 0,
                    'auth_token'     => '82lUb2FtK5r23n25isE3EgUrQDKm8F',
                    'verified_token' => 'd3YxD0rQ3mlNHa4uPOIVT6luDkigzS',
                    'template'       => 'default',
                    'created_at'     => 'vss-03-28dd',
                ],
                UserException::INVALID_CREATED_AT_VALUE,
            ],
        ];
    }
}
