<?php

declare(strict_types=1);

namespace WalkWeb\NW;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    // TODO Доработать - если auth = false, то user и password не используются и не проверяются

    public const ERROR_INVALID_HOST     = 'Incorrect parameter "smtp_host" it required and type string';
    public const ERROR_INVALID_PORT     = 'Incorrect parameter "smtp_port" it required and type int';
    public const ERROR_INVALID_AUTH     = 'Incorrect parameter "smtp_auth" it required and type bool';
    public const ERROR_INVALID_USER     = 'Incorrect parameter "smtp_user" it required and type string';
    public const ERROR_INVALID_PASSWORD = 'Incorrect parameter "smtp_password" it required and type string';
    public const ERROR_INVALID_FROM     = 'Incorrect parameter "from" it required and type string';

    private Container $container;
    private PHPMailer $mail;

    /**
     * @param Container $container
     * @param array $config
     * @throws AppException
     */
    public function __construct(Container $container, array $config)
    {
        $this->container = $container;
        $this->validateConfig($config);
        try {
            $this->mail = new PHPMailer(true);
            $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
            $this->mail->isSMTP();
            $this->mail->Host = $config['smtp_host'];
            $this->mail->Port = $config['smtp_port'];
            $this->mail->SMTPAuth = $config['smtp_auth'];
            $this->mail->Username = $config['smtp_user'];
            $this->mail->Password = $config['smtp_password'];
            $this->mail->CharSet = 'UTF-8';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->isHTML(true);
            $this->mail->setFrom($config['from']);
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * Отправляет сообщение на указанный email адрес
     *
     * @param string $address
     * @param string $title
     * @param string $message
     * @throws AppException
     */
    public function send(string $address, string $title, string $message): void
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($address);
            $this->mail->Subject = $title;
            $this->mail->Body    = $message;
            $this->mail->AltBody = $message;

            if ($this->container->getAppEnv() !== Container::APP_TEST) {
                $this->mail->send();
            }

        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * @param array $config
     * @throws AppException
     */
    private function validateConfig(array $config): void
    {
        if (!array_key_exists('smtp_host', $config) || !is_string($config['smtp_host'])) {
            throw new AppException(self::ERROR_INVALID_HOST);
        }
        if (!array_key_exists('smtp_port', $config) || !is_int($config['smtp_port'])) {
            throw new AppException(self::ERROR_INVALID_PORT);
        }
        if (!array_key_exists('smtp_auth', $config) || !is_bool($config['smtp_auth'])) {
            throw new AppException(self::ERROR_INVALID_AUTH);
        }
        if (!array_key_exists('smtp_user', $config) || !is_string($config['smtp_user'])) {
            throw new AppException(self::ERROR_INVALID_USER);
        }
        if (!array_key_exists('smtp_password', $config) || !is_string($config['smtp_password'])) {
            throw new AppException(self::ERROR_INVALID_PASSWORD);
        }
        if (!array_key_exists('from', $config) || !is_string($config['from'])) {
            throw new AppException(self::ERROR_INVALID_FROM);
        }
    }
}
