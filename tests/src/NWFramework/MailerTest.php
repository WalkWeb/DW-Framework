<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\AppException;
use NW\Mailer;
use Tests\AbstractTest;

class MailerTest extends AbstractTest
{
    /**
     * Тест на успешную отправку email
     *
     * В режиме APP_ENV=test реальная отправка писем не происходит
     *
     * @throws AppException
     */
    public function testMailerSendSuccess(): void
    {
        $mailer = new Mailer($this->getContainer(), MAIL_CONFIG);
        $mailer->send('mail@mail.com', 'Hello!', 'Message');

        // Если никакой ошибки не произошло - считаем, что все ок
        self::assertTrue(true);
    }

    /**
     * Тест на ситуацию, когда указан некорректный email для отправки
     *
     * @throws AppException
     */
    public function testMailerInvalidEmail(): void
    {
        $mailer = new Mailer($this->getContainer(), MAIL_CONFIG);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Invalid address:  (to): invalid_email');
        $mailer->send('invalid_email', 'Hello!', 'Message');
    }

    /**
     * Тесты на различные варианты некорректного конфига
     *
     * @dataProvider invalidConfigDataProvider
     * @param array $config
     * @param string $error
     * @throws AppException
     */
    public function testMailInvalidConfig(array $config, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        new Mailer($this->getContainer(), $config);
    }

    /**
     * @return array
     */
    public function invalidConfigDataProvider(): array
    {
        return [
            // Отсутствует smtp_host
            [
                [
                    'smtp_port'     => 465,
                    'smtp_auth'     => true,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 'smtp_password',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_HOST,
            ],
            // smtp_host некорректного типа
            [
                [
                    'smtp_host'     => 123,
                    'smtp_port'     => 465,
                    'smtp_auth'     => true,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 'smtp_password',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_HOST,
            ],
            // Отсутствует smtp_port
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_auth'     => true,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 'smtp_password',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_PORT,
            ],
            // smtp_port некорректного типа
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => '465',
                    'smtp_auth'     => true,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 'smtp_password',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_PORT,
            ],
            // Отсутствует smtp_auth
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => 465,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 'smtp_password',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_AUTH,
            ],
            // smtp_auth некорректного типа
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => 465,
                    'smtp_auth'     => null,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 'smtp_password',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_AUTH,
            ],
            // Отсутствует smtp_user
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => 465,
                    'smtp_auth'     => true,
                    'smtp_password' => 'smtp_password',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_USER,
            ],
            // smtp_user некорректного типа
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => 465,
                    'smtp_auth'     => true,
                    'smtp_user'     => true,
                    'smtp_password' => 'smtp_password',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_USER,
            ],
            // Отсутствует smtp_password
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => 465,
                    'smtp_auth'     => true,
                    'smtp_user'     => 'smtp_user',
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_PASSWORD,
            ],
            // smtp_password некорректного типа
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => 465,
                    'smtp_auth'     => true,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 123,
                    'from'          => 'mail@mail.com',
                ],
                Mailer::ERROR_INVALID_PASSWORD,
            ],
            // Отсутствует from
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => 465,
                    'smtp_auth'     => true,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 'smtp_password',
                ],
                Mailer::ERROR_INVALID_FROM,
            ],
            // from некорректного типа
            [
                [
                    'smtp_host'     => 'smtp_host',
                    'smtp_port'     => 465,
                    'smtp_auth'     => true,
                    'smtp_user'     => 'smtp_user',
                    'smtp_password' => 'smtp_password',
                    'from'          => ['mail@mail.com'],
                ],
                Mailer::ERROR_INVALID_FROM,
            ],
        ];
    }
}
